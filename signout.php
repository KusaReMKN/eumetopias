<?php

require_once('./environ.php');

session_begin();

/* Remove cookie */
$_SESSION = array();
if (ini_get('session.use_cookies')) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000, $params['path'],
		$params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();

quit:
header('HTTP/1.1 303 See Other');
header('Location: ./');
