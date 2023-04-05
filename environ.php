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
