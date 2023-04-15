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

	try {
		$db = new SQLite3(dbFile);
		$db->enableExceptions(true);
		$db->busyTimeout(3_000_000);

		/* Is name already in use? */
		$stmt = $db->prepare(
			'SELECT userId, pwHash, display FROM Users WHERE name=:name;'
		);
		$stmt->bindValue(':name', $name, SQLITE3_TEXT);
		$result = $stmt->execute();
		$row = $result->fetchArray();
		$pwHash = $row['pwHash'] ?? null;
		if (empty($pwHash)) {
			$status = 'wrong';
			goto quit2;
		}
		$result->finalize();
		$stmt->close();

		/* Verification */
		if (!password_verify($passwd, $pwHash)) {
			$status = 'wrong';
			goto quit2;
		}
		session_begin();
		session_regenerate_id(true);
		$_SESSION['userId']  = $row['userId'];
		$_SESSION['name']    = $name;
		$_SESSION['display'] = $row['display'];
quit2:
		$db->close();
	} catch (Exception $err) {
		die("Something wrong: $err");
	}
quit:
	header('HTTP/1.1 303 See Other');
	$location = strlen($status) === 0 ? './' : "./signin.php?status=$status&name=$name";
	header("Location: $location");
	die("Click <a href='$location'>here</a> to continue...");
}

$name   = htmlspecialchars($_GET['name'] ?? '');
$status = $_GET['status'] ?? null;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>さいんいん | Eumetopias</title>
<link rel="stylesheet" href="./css/normalize.css">
<link rel="stylesheet" href="./css/new.css">
<link rel="stylesheet" href="./css/eumetopias.css">
</head>
<body>
<header>
<h1>さいんいん</h1>
<div class="right">
<a href="./">とっぷ</a>
</div>
</header>
<form id="signin" method="POST">
<fieldset>
<?= $status === 'wrong' ? '<p>ユーザ名とかパスワードとかちょっと違うっぽいです……</p>' : '' ?>
<div>
<label for="name">ユーザ名:</label>
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
<input type="submit" id="submit" value="さいんいんする">
<p>
そういえば<a href="./signup.php">アカウント持ってなかったわ</a>。
</p>
</div>
</fieldset>
</form>
</body>
</html>
