<?php
define("PROXY", true);

$logfile = fopen("PATH/TO/LOG", "a");
function debuglog($message) {
	global $logfile;
	fwrite($logfile, date("r")."\t$message\n");
	fflush($logfile);
}
function uniord($c, $i, &$s) {
	$h = ord($c{0 + $i});
	if ($h <= 0x7F) {
		$s = 1;
		return $h;
	} else if ($h < 0xC2) {
		$s = 0;
		return false;
	} else if ($h <= 0xDF) {
		$s = 2;
		return ($h & 0x1F) << 6 | (ord($c{1 + $i}) & 0x3F);
	} else if ($h <= 0xEF) {
		$s = 3;
		return ($h & 0x0F) << 12 | (ord($c{1 + $i}) & 0x3F) << 6
					 | (ord($c{2 + $i}) & 0x3F);
	} else if ($h <= 0xF4) {
		$s = 4;
		return ($h & 0x0F) << 18 | (ord($c{1 + $i}) & 0x3F) << 12
					 | (ord($c{2 + $i}) & 0x3F) << 6
					 | (ord($c{3 + $i}) & 0x3F);
	} else {
		$s = 0;
		return false;
	}
}
function clean($s) {
	$c = "";
	for ($i = 0; $i < strlen($s); ++$i) {
		$o = uniord($s, $i, $size);
		if ($size == 0)
			$o = ord($s{$i});
		else
			$i += $size - 1;
		if ($o > 127)
			$c .= "&#$o;";
		else
			$c .= chr($o);
	}
	return $c;
}
$currentProxy = false;
function getProxy($new = false)
{
	global $currentProxy;
	global $db;
	if ($new || $currentProxy === false) {
		if (PROXY && $currentProxy !== false) {
			debuglog($currentProxy->host.":".$currentProxy->port." of type ".$currentProxy->type." is dead.");
			$db->query("UPDATE proxies SET dead = 1 WHERE id = ".$currentProxy->id);
		}
		if (PROXY) {
			$currentProxy = $db->query("SELECT id, host, port, type FROM proxies WHERE dead = 0 ORDER BY RAND() LIMIT 1")->fetch_object();
			debuglog("Choosing proxy ".$currentProxy->host.":".$currentProxy->port." of type ".$currentProxy->type.".");
		}
		$currentProxy->useragent = $db->query("SELECT useragent FROM useragents ORDER BY RAND() LIMIT 1")->fetch_object()->useragent;
		debuglog("Choosing UA ".$currentProxy->useragent.".");

	}
	return $currentProxy;
}
function file_get_contents_curl($url) {
	$proxy = getProxy();
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, $proxy->useragent);
	if (PROXY) {
		curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy->type == 1 ? CURLPROXY_SOCKS5 : CURLPROXY_HTTP);
		curl_setopt($ch, CURLOPT_PROXY, $proxy->host);
		curl_setopt($ch, CURLOPT_PROXYPORT, $proxy->port);
	}
	curl_setopt($ch, CURLOPT_TIMEOUT, 50);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$data = curl_exec($ch);
	if (PROXY && ($data == "" || stripos($data, "Your cache administrator") !== false || stripos($data, "requested page is currently unavailable") !== false || stripos($data, "500 Internal Server Error") !== false || stripos($data, "Cache Access Denied") !== false)) {
		getProxy(true);
		sleep(1);
		return file_get_contents_curl($url);
	}
	curl_close($ch);
	return $data;
}
error_reporting(E_ERROR | E_PARSE);

$db = new mysqli("SERVER", "USERNAME", "PASSWORD", "DATABASE");

require_once("simplepie.inc");

debuglog("Fetching rss feed.");
$posts = new SimplePie();
$posts->enable_cache(false);
$posts->set_raw_data(file_get_contents_curl("http://www.bwog.com/feed"));
$posts->init();
foreach ($posts->get_items() as $post) {
	$link = $db->real_escape_string($post->get_permalink());
	$title = $db->real_escape_string(clean($post->get_title()));
	$id = $post->get_id();
	$id = (int)substr($id, strpos($id, "p=") + 2);
	$date = $db->real_escape_string($post->get_date('c'));
	$db->query("INSERT IGNORE INTO posts VALUES ('$id', '$date', '$title', '$link')");
}
$latestPosts = $db->query("SELECT id, url FROM posts ORDER BY date DESC LIMIT 40");
while ($post = $latestPosts->fetch_object()) {
	debuglog("Fetching {$post->url}.");
	$html = file_get_contents_curl($post->url);
	preg_match_all('(<div id="comment-([0-9]{3,8})" class=".*>\s*<div class="comment-info">\s*<cite class="fn">([^<>]*)[<]{0,1}.*</cite>\s*.*\s*([A-Za-z0-9: ]*) at ([A-Za-z0-9: ]*)<.*\s*.*\s*.*\s*.*\s*([\s|\S]*?)\s*</div>)', $html, $matches, PREG_SET_ORDER);

	$ids = array();
	$contents = array();
	foreach ($matches as $match) {
		$id = (int)$match[1];
		$ids[] = $id;
		$author = $db->real_escape_string($match[2]);
		$date = $db->real_escape_string(strftime("%F %T", strtotime($match[3]." ".$match[4])));
		$content = $match[5];
		if (strpos($content, "<div") === 0)
			$content = substr($content, strpos($content, ">") + 1);
		$contents[$id] = $content;
		$content = $db->real_escape_string($content);
		debuglog("Found $id from {$post->id}");
		$db->query("INSERT INTO comments VALUES('$id', '{$post->id}', '$date', '$author', '$content', 0, 0, 0) ON DUPLICATE KEY UPDATE censored=0");
	}

	$modified = array();
	$notmodified = array();
	$doubleCheck = $db->query("SELECT id, content FROM comments WHERE post_id={$post->id}");
	while ($checkPost = $doubleCheck->fetch_object()) {
		if (!isset($contents[$checkPost->id]))
			continue;
		if ($checkPost->content != $contents[$checkPost->id]) {
			debuglog("{$checkPost->id} has been modified by Bwog.");
			$modified[] = $checkPost->id;
		} else
			$notmodified[] = $checkPost->id;
	}
	$db->query("UPDATE comments SET modified=1 WHERE ".modifiedList($modified));
	$db->query("UPDATE comments SET modified=0 WHERE ".modifiedList($notmodified));

	$idquery = "(";
	for ($i = 0; $i < count($ids); ++$i) {
		$idquery .= $ids[$i];
		if ($i != count($ids) - 1)
			$idquery .= ", ";
		else
			$idquery .= ")";
	}
	$db->query("UPDATE comments SET censored=1 WHERE post_id={$post->id} AND censored=0 AND id NOT IN $idquery");
}
function modifiedList($modified)
{
	$modifiedquery = "";
	for ($i = 0; $i < count($modified); ++$i) {
		$modifiedquery .= "id=".$modified[$i];
		if ($i != count($modified) - 1)
			$modifiedquery .= " OR ";
	}
	return $modifiedquery;
}
?>
