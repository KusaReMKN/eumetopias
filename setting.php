<?php

require_once('./environ.php');
require_once('./settings.php');

forceHttps();
session_begin();

if (empty($_SESSION['userId'])) {
	header('HTTP/1.1 403 Forbidden');
	die('You must <a href="./signin.php">sign in</a>.');
}

$userId  = $_SESSION['userId'];
$name    = htmlspecialchars($_SESSION['name']);
$display = htmlspecialchars($_SESSION['display'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$status = $_POST['setting'] . '_';
	switch ($_POST['setting']) {
	case 'display':
		$status .= setting_display($userId, $_POST['display'] ?? '');
		break;
	case 'passwd':
		$status .= setting_passwd($userId, $_POST['prev'] ?? '', $_POST['passwd'] ?? '');
		break;
	default:
		$status .= 'unknown';
		break;
	}

	header('HTTP/1.1 303 See Other');
	$location = "./setting.php?status=$status#setting_{$_POST['setting']}";
	header("Location: $location");
	die("Click <a href='$location'>here</a> to continue...");
}
$status = $_GET['status'] ?? '';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>せってい | Eumetopias</title>
<link rel="stylesheet" href="./css/normalize.css">
<link rel="stylesheet" href="./css/new.css">
<link rel="stylesheet" href="./css/eumetopias.css">
</head>
<body>
<header>
<h1>せってい</h1>
<div class="right">
<a href="./">とっぷ</a>
</div>
</header>
<form id="setting_display" method="POST">
<fieldset>
<legend>表示用の名前</legend>
<p>
インターフェースに表示するための名前を設定できます。
空文字列を指定するとユーザ名 (<b><?= $name ?></b>) がそのまま表示されます。
</p>
<div>
<label for="display">表示用の名前:</label>
<br>
<input type="text" id="display" name="display" value="<?= $display ?>" placeholder=".*">
</div>
<hr>
<div>
<input type="hidden" name="setting" value="display">
<input type="submit" id="display_submit" value="表示用の名前を設定する">
</div>
<?php
if (strpos($status, 'display_') === 0)
	if (strpos($status, 'OK') !== false)
		print('<div>ちゃんと変更されたっぽいです</div>');
	else
		print('<div>なんかトチったっぽいです</div>');
?>
</fieldset>
</form>
<form id="setting_passwd" method="POST">
<fieldset>
<legend>パスワードの変更</legend>
<p>
パスワードを変更できます。
</p>
<div>
<label for="prevpasswd">今までのパスワード:</label>
<br>
<input type="password" id="prevpasswd" name="prev" placeholder=".{8,}" minlength="8" required>
</div>
<div>
<label for="newpasswd">新しいパスワード:</label>
<br>
<input type="password" id="newpasswd" name="passwd" placeholder=".{8,}" minlength="8" required>
</div>
<hr>
<div>
<input type="hidden" name="setting" value="passwd">
<input type="submit" id="password_submit" value="パスワードを変更する">
</div>
<?php
if (strpos($status, 'passwd_') === 0)
	if (strpos($status, 'OK') !== false)
		print('<div>ちゃんと変更されたっぽいです</div>');
	else if (strpos($status, '2SHORT') !== false)
		print('<div>パスワードがちょっと弱過ぎるっぽいです</div>');
	else if (strpos($status, 'DENIED') !== false)
		printf('<div>今までのパスワードがちょっと違うっぽいです</div>');
	else
		print('<div>なんかトチったっぽいです</div>');
?>
</fieldset>
</form>
</body>
</html>
