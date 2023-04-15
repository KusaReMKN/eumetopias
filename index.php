<?php

require_once('./environ.php');

forceHttps();
session_begin();

if (empty($_GET['user'])) {
	if (empty($_SESSION['userId'])) {
		require_once('./gotaku.html');
		die();
	}
	header('HTTP/1.1 303 See Other');
	$location = "./?user=${_SESSION['name']}";
	header("Location: $location");
	die("Click <a href='$location'>here</a> to continue...");
}

try {
	$db = new SQLite3(dbFile);
	$db->enableExceptions(true);
	$db->busyTimeout(3_000_000);

	$stmt = $db->prepare(
		'SELECT userId, display FROM Users WHERE name=:name;'
	);
	$stmt->bindValue(':name', $_GET['user'], SQLITE3_TEXT);
	$result = $stmt->execute();
	$row = $result->fetchArray();
	$name = htmlspecialchars($row['display'] ?? $_GET['user']);
	if (empty($row['userId'])) {
		header('HTTP/1.1 404 Not Found');
		die("$name: unknown user. <a href='./'>Go back</a>");
	}
	$userId = $row['userId'];
	$result->finalize();
	$stmt->close();

	$stmt = $db->prepare(
		'SELECT taskId, title, currPri FROM Tasks WHERE owner=:owner;'
	);
	$stmt->bindValue(':owner', $userId, SQLITE3_INTEGER);
	$result = $stmt->execute();
	$tasks = [];
	while (($row = $result->fetchArray()) !== false)
		$tasks[] = $row;
} catch (Exception $err) {
	die("Something wrong: $err");
}

$you = htmlspecialchars($_SESSION['display'] ?? $_SESSION['name']);
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
<?= $you ?> としてログイン中。
<a href="./setting.php">せってい</a> / <a href="./signout.php">さいんあうと</a>
</div>
</header>
<?= count($tasks) ?>
<?php print_r($tasks); ?>
</body>
</html>
