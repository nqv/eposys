<?php
/*
--------------------------------------------------------------------------------
     File:  common.php

     Unit:  COMMON DATABASE
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-03-11

  Comment:
--------------------------------------------------------------------------------
*/

if (!defined('IN_EPS'))
	exit;

switch ($eps_db_config['type'])
{
	case 'mysqli':
		require_once EPS_CLASS_DIR.'class_mysqli.php';
		break;
	default:
		require_once EPS_CLASS_DIR.'class_mysql.php';
}

$epsclass->load_class(array('extend_db', 'extend_db', 'db'), $eps_db_config['host'], $eps_db_config['user'], $eps_db_config['pass'], $eps_db_config['name'], $eps_db_config['p_connect']);
unset($eps_db_config['pass']);
?>
