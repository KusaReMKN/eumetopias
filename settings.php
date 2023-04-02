<?php

require_once('./environ.php');

function setting_display(int $userId, string $display)
{
	if (strlen($display) === 0)
		$display = null;

	try {
		$db = new SQLite3(dbFile);
		$db->enableExceptions(true);
		$db->busyTimeout(3_000_000);
		$stmt = $db->prepare(
			'UPDATE Users SET display=:display WHERE userId=:userId;'
		);
		$stmt->bindValue(':display', $display, SQLITE3_TEXT);
		$stmt->bindValue(':userId', $userId, SQLITE3_INTEGER);
		$stmt->execute();
		$stmt->close();
	} catch (Exception $err) {
		return 'NG';
	}
	$_SESSION['display'] = $display;

	return 'OK';
}

function setting_passwd(int $userId, string $previous, string $passwd)
{
	if (strlen($passwd) < 8)
		return '2SHORT';

	try {
		$db = new SQLite3(dbFile);
		$db->enableExceptions(true);
		$db->busyTimeout(3_000_000);
		$stmt = $db->prepare(
			'SELECT pwHash FROM Users WHERE userId=:userId;'
		);
		$stmt->bindValue(':userId', $userId, SQLITE3_INTEGER);
		$result = $stmt->execute();
		$pwHash = $result->fetchArray()['pwHash'] ?? null;
		$stmt->close();
		if (!password_verify($previous, $pwHash))
			return 'DENIED';

		$pwHash = password_hash($passwd, PASSWORD_DEFAULT);
		$stmt = $db->prepare(
			'UPDATE Users SET pwHash=:pwHash WHERE userId=:userId;'
		);
		$stmt->bindValue(':pwHash', $pwHash, SQLITE3_TEXT);
		$stmt->bindValue(':userId', $userId, SQLITE3_INTEGER);
		$stmt->execute();
		$stmt->close();
	} catch (Exception $err) {
		return 'NG';
	}

	return 'OK';
}
