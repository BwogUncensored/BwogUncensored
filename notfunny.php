<?php
session_start();
if (isset($_GET["login"])) {
	if ($_POST["username"] == "THEUSERNAME" && $_POST["password"] == "THEPASSWORD") {
		$_SESSION["role"] = "admin";
		header("Location: /?censor");
	} else {
		session_destroy();
?>
<form method="post">
username: <input name="username" /><br />
password: <input type="password" name="password" /><br />
<input type="submit" />
</form>
<?php
	}
	exit();
}


$id = (int)$_GET["id"];
if ($_SESSION["role"] != "admin" || $id == 0) {
	header("Status: 404 Not Found");
	die();
}
if ($_GET["undo"] == "true")
	$funnyValue = 0;
else
	$funnyValue = 1;
$db = new mysqli("SERVER", "USERNAME", "PASSWORD", "DATABASE");
$db->query("UPDATE comments SET notfunny=$funnyValue WHERE id=$id");
?>
