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
		'SELECT taskId, title, priTxt FROM Tasks INNER JOIN Priorities ON currPri=priId'
		. ' WHERE owner=:owner ORDER BY ctime DESC, currPri DESC;'
	);
	$stmt->bindValue(':owner', $userId, SQLITE3_INTEGER);
	$result = $stmt->execute();
	$tasks = [];
	while (($row = $result->fetchArray()) !== false)
		$tasks[] = $row;
	$result->finalize();
	$stmt->close();
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
<main>
<?php
if (count($row) > 0) {
	echo <<<EOHTML
<table>
<thead>
<tr>
<th>やること</th>
<th>やばさ</th>
</tr>
</thead>
<tbody>
EOHTML;
	foreach ($tasks as $row) {
		$taskId = htmlspecialchars($row['taskId']);
		$title  = htmlspecialchars($row['title']);
		$priTxt = htmlspecialchars($row['priTxt']);
		echo '<tr>';
		echo "<td><a href='./?task=$taskId'>$title</a></td>";
		echo "<td>$priTxt</td>";
		echo '</tr>';
	}
	echo <<<EOHTML
</tbody>
</table>
EOHTML;
} else {
	echo "<p>$name さんのやることはないようです</p>";
}
?>
</main>
</body>
</html>
