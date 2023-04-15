<?php

require_once('./environ.php');

forceHttps();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$status = '';

	/* Missing parameter? */
	if (empty($_POST['name']) || empty($_POST['passwd']))
		goto quit;
	$name   = $_POST['name'];
	$passwd = $_POST['passwd'];

	if (strlen($passwd) < 8) {
		$status = 'password';
		goto quit;
	}

	try {
		$db = new SQLite3(dbFile);
		$db->enableExceptions(true);
		$db->busyTimeout(3_000_000);
		$db->exec('BEGIN TRANSACTION;');
		try {
			/* Is name already in use? */
			$stmt = $db->prepare(
				'SELECT COUNT(userId) FROM Users WHERE name=:name;'
			);
			$stmt->bindValue(':name', $name, SQLITE3_TEXT);
			$result = $stmt->execute();
			if ($result->fetchArray()[0] !== 0) {
				$status = 'username';
				goto quit2;
			}
			$result->finalize();
			$stmt->close();

			/* Registration */
			$pwHash = password_hash($passwd, PASSWORD_DEFAULT);
			$stmt = $db->prepare(
				'INSERT INTO Users ( name, pwHash ) values ( :name, :pwHash );'
			);
			$stmt->bindValue(':name', $name, SQLITE3_TEXT);
			$stmt->bindValue(':pwHash', $pwHash, SQLITE3_TEXT);
			$stmt->execute();
			$stmt->close();

			/* Signing in */
			session_begin();
			session_regenerate_id(true);
			$_SESSION['userId'] = $db->lastInsertRowID();
			$_SESSION['name']   = $name;
		} catch (Exception $err) {
			$db->exec('ROLLBACK TRANSACTION;');
			throw $err;
		}
quit2:
		$db->exec('COMMIT TRANSACTION;');
		$db->close();
	} catch (Exception $err) {
		die("Something wrong: $err");
	}
quit:
	header('HTTP/1.1 303 See Other');
	$location = strlen($status) === 0 ? './' : "./signup.php?status=$status&name=$name";
	header("Location: $location");
	die("Click <a href='$location'>here</a> to continue...");
}

$name   = htmlspecialchars($_GET['name'] ?? '');
$status = $_GET['status'] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>さいんあっぷ | Eumetopias</title>
<link rel="stylesheet" href="./css/normalize.css">
<link rel="stylesheet" href="./css/new.css">
<link rel="stylesheet" href="./css/eumetopias.css">
</head>
<body>
<header>
<h1>さいんあっぷ</h1>
<div class="right">
<a href="./">とっぷ</a>
</div>
</header>
<form id="signup" method="POST">
<fieldset>
<?php
switch ($status) {
case 'username':
	print("<p><b>$name</b> って名前はもう使われてるっぽいです……</p>");
	break;
case 'password':
	printf("<p>パスワードがちょっと弱過ぎるっぽいです……</p>");
	break;
default:
	/* Nothing to do */
}
?>
<div>
<label for="name">ユーザ名 (あとで表示名を別に設定できます):</label>
<br>
<input type="text" id="name" name="name" value="<?= $name ?>" placeholder=".+" required>
</div>
<div>
<label for="passwd">パスワード:</label>
<br>
<input type="password" id="passwd" name="passwd" placeholder=".{8,}" minlength="8" required>
</div>
<hr>
<div>
<input type="submit" id="submit" value="さいんあっぷする">
<p>
そういえば<a href="./signin.php">アカウント持ってたわ</a>。
</p>
</div>
</fieldset>
</form>
</body>
</html>
