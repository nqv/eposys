<?php
/*
--------------------------------------------------------------------------------
     File:  common.php

     Unit:  COMMON
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2005-12-29

  Comment:
--------------------------------------------------------------------------------
*/

define('IN_EPS', true);

// Turn Off magic_quotes_runtime
set_magic_quotes_runtime(0);

// Protect against GLOBALS & _SESSION tricks
if (isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS']) || (isset($_SESSION) && !is_array($_SESSION)))
{
	exit;
}

// Be paranoid with passed vars
if (@ini_get('register_globals') == '1' || strtolower(@ini_get('register_globals')) == 'on')
{
	$not_unset = array('_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_SESSION', '_ENV', '_FILES');

	// Merge all
	$input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, (isset($_SESSION) && is_array($_SESSION)) ? $_SESSION : array());

	foreach ($input as $varname => $void)
	{
		if (!in_array($varname, $not_unset))
		{
			unset(${$varname});
		}
	}
	unset($input, $not_unset);
}

// Stripslash from _GET/_POST/_COOKIE
if (get_magic_quotes_gpc())
{
	function stripslashes_array($array)
	{
		return is_array($array) ? array_map('stripslashes_array', $array) : stripslashes($array);
	}
	$_GET = stripslashes_array($_GET);
	$_POST = stripslashes_array($_POST);
	$_COOKIE = stripslashes_array($_COOKIE);
	if (isset($_SESSION))
		$_SESSION = stripslashes_array($_SESSION);
}

// Initial
require_once EPS_ROOT.'include/config.php';
require_once EPS_ROOT.'include/parameter.php';
require_once EPS_ROOT.'include/function.php';

// Report Errors
if (defined('EPS_DEBUG'))
	error_reporting(E_ALL);
else
	error_reporting(E_ERROR | E_PARSE);

// Class Of Classes
require_once EPS_ROOT.'include/epsclass.php';
$epsclass = new epsclass();

// Database
require_once EPS_ROOT.'include/database.php';

@include_once FILE_CACHE_CONFIG;
if (!defined('CONFIG_LOADED'))
{
	create_config_file();
	require_once FILE_CACHE_CONFIG;
}

// Gzip
$_SERVER['HTTP_ACCEPT_ENCODING'] = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';
if ($eps_config['gzip'] && extension_loaded('zlib') && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false || strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') !== false))
	ob_start('ob_gzhandler');
else
	ob_start();

// Session
eps_session_start();

// Check User
$eps_user = array();
chk_user($eps_user);

// Language
@include_once EPS_ROOT.'lang/'.$eps_user['language'].'/lang_common.php';
if (!isset($eps_lang))
	@require_once EPS_ROOT.'lang/'.$eps_config['default_lang'].'/lang_common.php';

if (!isset($eps_lang))
	exit('There is no valid language pack \''.htmlspecialchars($eps_user['language']).'\' installed. Please reinstall a language of that name.');


// Smarty
require_once SMARTY_DIR.'Smarty.class.php';
require_once EPS_CLASS_DIR.'extend_smarty.php';
$smarty = new extend_smarty();
$smarty->assign('eps_config', $eps_config); 
$smarty->assign('eps_user', $eps_user);
$smarty->assign('eps_lang', $eps_lang);

// Maintenance?
if ($eps_config['maintenance'] && ($eps_user['is_guest'] || $eps_user['group_id'] > EPS_ADMIN))
	maintenance_message();

define('COMMON_LOADED', true);
