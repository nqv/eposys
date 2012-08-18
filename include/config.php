<?php
// Database
$eps_db_config = array(
	'host' => 'localhost',
	'user' => 'root',
	'pass' => '',
	'name' => 'eposys',
	'p_connect' => false,
	'type' => 'mysql'
);

// Cookie
$eps_cookie = array(
	'name' => 'eps_cookie',
	'expire' => 32850000,
	'path' => '/',
	'domain' => '',
	'secure' => 0,
	'seed' => '2131984'
);

// Session
$eps_session = array(
	'name' => 'eps_user_session'
);

define('EPS_DEBUG', true);

?>
