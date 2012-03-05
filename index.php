<?php
function queryUrl($query) {
	parse_str($query, $newQuery);
	return "/?".http_build_query(array_merge($_GET, $newQuery));
}
if (isset($_GET["censor"]))
	session_start();
if ($_GET["sort"] == "date") {
	$sortQuery = "comments.date DESC";
	$sortText = "Sorted by date. <a href=\"".queryUrl("sort=post")."\">Sort by post?</a>";
}
else {
	$sortQuery = "posts.date DESC, posts.id DESC, comments.date";
	$sortText = "Sorted by post. <a href=\"".queryUrl("sort=date")."\">Sort by date?</a>";
}
if ($_GET["notfunny"] == "show" || $_SESSION["role"] == "admin") {
	$notfunnyText = "You're currently viewing all the censored comments, including all the spam and repeated junk and the ones that just aren't very funny after the 1000th time. <a href=\"".queryUrl("notfunny=hide")."\">Hide the junk comments?</a>";
	$notfunnyQuery = "";
} else {
	$notfunnyText = "A lot of junk eventually hits Bwog Uncensored -- spam websites, jokes that aren't funny after the 1000th time, and other filth, so we don't show it on the main page. But Bwog Uncensored is not about censorship! <a href=\"".queryUrl("notfunny=show")."\">Show the junk comments?</a>";
	$notfunnyQuery = "AND comments.notfunny=0";
}
?>
<!DOCTYPE html>
<html lang="en">
<!-- go fuck yourself -->
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="description" content="Bwog Uncensored displays censored comments from Bwog. It's meant as a study on Internet censorship. Support our trolls!" />
	<meta name="keywords" content="bwog, the bwog, censorship, uncensored, censor, bwog uncensored, comments, censored comments, censored, democracy, free speech" />
	<meta name="author" content="Bwog Uncensored" />
	<meta http-equiv="refresh" content="600" />
	<?php if (isset($_GET["mirror"])): ?><style>
	html body {
		-webkit-transform: matrix(-1, 0, 0, 1, 0, 0);
		-moz-transform: matrix(-1, 0, 0, 1, 0, 0);
		transform: matrix(-1, 0, 0, 1, 0, 0);
		filter: progid:DXImageTransform.Microsoft.BasicImage(mirror=1);
	}
	</style><?php endif; ?>
	<title>Bwog Uncensored</title>
	<link href="styles.css" rel="stylesheet" type="text/css" />
	<script src="scripts.js" type="text/javascript"></script>
</head>
<body>
	<div id="header"><img src="nameplate.png" alt="Bwog Uncensored" /></div>
	<div id="navbar">Uncensored</div>
	<div id="leftbar" class="sidebar">
		<h1>News Update</h1>
		<p><em>March 2nd, 2011</em>: With the recent disclosure of the ListServ asshole, Bwog got smart and figured out how to trick us -- they changed the content of the post without removing it, defeating our ID system. Rest assured we have a work around. Now posts that Bwog has modified will be marked as such, like <a href="#comment-230623">this comment</a>, for example.</p>
		<p><em>February 17th, 2011</em>: We haven't posted for a while, but rest assured, Bwog Uncensored is still alive and kicking. We're just a bit busy with school at the moment, but rest assured, we will be very visible again shortly enough. In the mean while, it seems that Bwog has <a href="http://www.spoofurl.com?http://bwog.com/2011/02/08/ruckus/#comment-227153" target="_blank">in fact cut down on censoring, as noted by some Bwog commenters</a>. We'll be back with you soon.</p>
		<p><em>January 19th, 2011</em>: Evidently the string "bwoguncensored" has been unblocked after yesterday's scuffle. We've heard reports that Bwog editors are absolutely irate, but are now trying to downplay the significance and lay low on censoring for a bit. They hope we'll disappear. We won't.</p>
		<p><em>January 18th, 2011</em>: It seems as if Bwog has blocked our IP addresses, which means in the last few hours we've missed any censored comments. But fear not -- we're now using a fully distributed model which means in order to block us, they'd have to block most of the net. We've also made our requests to bwog fully generic so that there exist no heuristic tests for determining which requests are ours. Further, as expected, in addition to censoring the word "bwoguncensored", <s>they're censoring comments critical of Bwog editors themselves that are in favor of Bwog Uncensored, like <a href="#comment-224944">this comment</a></s>. <em>Update</em>: Bwog put that comment back up, evidently responding to our criticism. Victory. You can read the <a href="http://www.spoofurl.com?http://bwog.com/2011/01/18/its-over-already-next-hearing-scheduled-for-march-1st/#comments" target="_blank">little scuffle</a> between our supporters and Bwog editors.</p>
		<p><em>January 17th, 2011</em>: Welcome to the launch of Bwog Uncensored. Generally Bwog censors in large batches during various scandalous times. We'll be sure to inform you of the right times to check back on this site to see what they've been censoring. But until then, below is what has been censored in the last couple of months. In this particular list, the funnier comments seem to be in the middle-bottom, but there are gems all over. Have a blast.</p>
	</div>
	<div id="rightbar" class="sidebar">
		<h1>About Us</h1>
		<p>The editors at <a href="http://www.spoofurl.com?http://www.bwog.com" target="_blank">Bwog</a> censor your comments. We make public the silenced voices of our championed dissidents who fight the hard fight. Support our trolls.</p>
		<h1>Help The Cause</h1>
		<p>Want to help Bwog Uncensored? Spread the word! <a href="flyers.pdf">Download flyers</a> to give to your friends. Or head on over to <a href="http://www.spoofurl.com?http://www.bwog.com" target="_blank">Bwog</a> and see if you can get your comment censored. And if you would like to help our efforts, feel free to write to us at <a href="mailto:join@bwoguncensored.com">join@&#8203;bwog&#8203;uncensored&#8203;.com</a>.</p>
		<h1>Our Design</h1>
		<p>This site looks a lot like <a href="http://www.spoofurl.com?http://www.bwog.com" target="_blank">Bwog</a>'s, but it isn't. We're not affiliated with them (see our <a href="#footer">disclaimer</a>). This site's design was created via a <a href="http://en.wikipedia.org/wiki/Clean_room_design" target="_blank">clean-room process</a>. All of the code, layout, and graphics were created from scratch by sight...even the logo at the top is not quite the same. Looks pretty good, doesn't it? We want to drive home Bwog Uncensored's relationship to Bwog's comments without stealing a single ounce of their webpage's code.</p>
		<h1>Contact</h1>
		<p>Any inquiries or comments may be sent to <a href="mailto:info@bwoguncensored.com">info@&#8203;bwog&#8203;uncensored&#8203;.com</a>. Complaints or concerns may be sent to <a href="mailto:complaints@bwoguncensored.com">complaints@&#8203;bwog&#8203;uncensored&#8203;.com</a>. Be sure to read our <a href="#footer">disclaimer</a>.</p>
	</div>
	<div id="content">
		<div id="sorting"><?php echo $sortText; ?></div>
<?php
$db = new mysqli("SERVER", "USERNAME", "PASSWORD", "DATABASE");
$censored = $db->query("SELECT posts.title, posts.url, comments.content, comments.author, comments.date, comments.id, comments.post_id, comments.notfunny, comments.modified FROM posts, comments WHERE posts.id = comments.post_id AND (comments.censored=1 OR comments.modified=1) $notfunnyQuery ORDER BY $sortQuery");
$lastId = 0;
while ($comment = $censored->fetch_object()) {
	if ($lastId != $comment->post_id) {
		$lastId = $comment->post_id;
		echo "\t\t<h2>From the article <a href=\"http://www.spoofurl.com?{$comment->url}#comments\" target=\"_blank\">{$comment->title}</a>:</h2>\n";
	}
	echo "\t\t<div class=\"comment\" id=\"comment-{$comment->id}\">\n";
	$modified = "";
	if ($comment->modified)
		$modified = " &middot; <span class=\"modified\">original <a href=\"http://www.spoofurl.com?{$comment->url}#comment-{$comment->id}\">modified</a> by Bwog</span>";
	echo "\t\t\t<div class=\"commentinfo\"><cite>{$comment->author}</cite> &middot; <a href=\"http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."#comment-{$comment->id}\">".strftime("%e %B %Y at %l:%M %p", strtotime($comment->date))."</a>$modified</div>\n";
	echo "\t\t\t{$comment->content}\n";
	if ($_SESSION["role"] == "admin") {
		if ($comment->notfunny) {
			$displayFunny = " style=\"display: none\"";
			$displayNotFunny = "";
		} else {
			$displayNotFunny = " style=\"display: none\"";
			$displayFunny = "";
		}
		echo "\t\t\t<p$displayFunny id=\"{$comment->id}-notfunny\"><b><a href=\"javascript:(function(){var v = new XMLHttpRequest();v.open('get','notfunny.php?undo=false&id={$comment->id}',false);v.send(null);document.getElementById('{$comment->id}-funny').style.display = 'block';document.getElementById('{$comment->id}-notfunny').style.display = 'none';})();\">Not funny?</a></b></p>\n";
		echo "\t\t\t<p$displayNotFunny id=\"{$comment->id}-funny\"><b><a href=\"javascript:(function(){var v = new XMLHttpRequest();v.open('get','notfunny.php?undo=true&id={$comment->id}',false);v.send(null);document.getElementById('{$comment->id}-notfunny').style.display = 'block';document.getElementById('{$comment->id}-funny').style.display = 'none';})();\">Is funny?</a></b></p>\n";
	}
	echo "\t\t</div>\n";
}
?>
		<div id="sorting"><?php echo $notfunnyText; ?></div>
	</div>
	<div id="footer">This website is not associated with Bwog, The Blue &amp; White, nor any academic institution. The comments on this page do not necessarily reflect the opinions, beliefs, positions, or factual assertions of Bwog Uncensored, Bwog, The Blue &amp; White, nor any academic institutional. The comments are the property of their respective authors. The comments on this page do not necessarily reflect the opinions, beliefs, positions, or factual assertions of their respective authors. The individuals and/or institutions mentioned on this page as author of a comment or mentioned in a comment do not necessarily correlate to their actual authors, actual individuals and/or institutions mentioned, or facts pertaining to such individuals, institutions, and/or authors. Bwog Uncensored reserves the right to modify, add, or remove any content of this website without restriction or notice.</div>
</body>
</html>
