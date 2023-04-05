<?php

require_once('./environ.php');

session_begin();

$name = htmlspecialchars($_SESSION['display'] ?? $_SESSION['name'] ?? '');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Eumetopias</title>
<link rel="stylesheet" href="./css/normalize.css">
<link rel="stylesheet" href="./css/new.css">
<link rel="stylesheet" href="./css/eumetopias.css">
</head>
<body>
<header>
<h1>Eumetopias</h1>
<div class="right">
<?=
strlen($name) === 0
	? '<a href="./signin.php">さいんいん</a> / <a href="./signup.php">さいんあっぷ</a>'
	: '<a href="./setting.php">せってい</a> / <a href="./signout.php">さいんあうと</a>';
?>
</div>
</header>
<?= strlen($name) !== 0 ? "<p>こんにちは、$name さん！ やることはやった？</p>" : '' ?>
</body>
</html>
