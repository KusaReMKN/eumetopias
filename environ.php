<?php

const dbFile = './eumetopias.db';
const sessionName = 'Eumetopias';

function session_begin()
{
	$path   = dirname($_SERVER['PHP_SELF']) . '/';
	$domain = '.' . $_SERVER['SERVER_NAME'];	/* XXX: security risk */

	session_name(sessionName);
	session_set_cookie_params(604_800, $path, $domain, true, true);
	session_start();
}

function forceHttps()
{
	if (empty($_SERVER['HTTPS']) && $_SERVER['REQUEST_SCHEME'] !== 'https'
			&& $_SERVER['HTTP_X_FORWARDED_PROTO'] !== 'https') {
		header('HTTP/1.1 307 Temporary Redirect');
		$location = "https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
		header("Location: $location");
		die("Click <a href='$location'>here</a> to continue...");
	}
}
