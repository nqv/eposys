<?php
/*
--------------------------------------------------------------------------------
     File:  function.php

  Project:  Common Function
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2005-12-27

--------------------------------------------------------------------------------
*/

if (!defined('IN_EPS'))
	exit;

// Start Session
function eps_session_start()
{
	if (!defined('SESSION_STARTED'))
	{
		session_start();
		define('SESSION_STARTED', true);
	}
}

// Set Cookie & Session (User_id, Password, Setcookie = true)
function set_user($user_id, $password, $remember = true)
{
	global $eps_session, $eps_cookie;

	if ($user_id == 0)
	{
		$_SESSION[$eps_session['name']] = '';
		setcookie($eps_cookie['name'], 0, time() + $eps_cookie['expire'], $eps_cookie['path'], $eps_cookie['domain'], $eps_cookie['secure']);
		set_guest();
		return;
	}

	$user_hash = serialize(array($user_id, md5($eps_cookie['seed'].$password)));

	// Session
	eps_session_start();
	$_SESSION[$eps_session['name']] = $user_hash;

	// Cookie
	setcookie($eps_cookie['name'], $user_hash, ($remember) ? (time() + $eps_cookie['expire']) : 0, $eps_cookie['path'], $eps_cookie['domain'], $eps_cookie['secure']);
}

// Check User (User)
function chk_user(&$eps_user)
{
	global $epsclass, $eps_config, $eps_cookie, $eps_session;

	eps_session_start();

	// Set Guest
	$cookie = array('user_id' => 0, 'password_hash' => 0);

	// Get User_id And Password_hash
	if (!empty($_SESSION[$eps_session['name']]))
		list($cookie['user_id'], $cookie['password_hash']) = @unserialize($_SESSION[$eps_session['name']]);
	else if (!empty($_COOKIE[$eps_cookie['name']]))
		list($cookie['user_id'], $cookie['password_hash']) = @unserialize($_COOKIE[$eps_cookie['name']]);

	if ($cookie['user_id'] > 0)
	{
		// Check User
		$result = $epsclass->db->query("SELECT g.g_title,u.* FROM ".TBL_USER." u LEFT JOIN ".TBL_GROUP." g ON u.group_id=g.g_id WHERE u.id=".intval($cookie['user_id'])) or error('Unable to fetch user information', __FILE__, __LINE__, $epsclass->db->error());
		$eps_user = $epsclass->db->fetch_assoc($result);
		$epsclass->db->free_result($result);

		// Authorisation Failed
		if (!isset($eps_user['id']) || md5($eps_cookie['seed'].$eps_user['password']) != $cookie['password_hash'])
		{
			set_user(0, 0);
			return;
		}

		// ADMIN
		if ($eps_user['group_id'] == EPS_ADMIN)
			define('IS_ADMIN', true);
		else
			define('IS_ADMIN', false);

		// MODADMIN
		if ($eps_user['group_id'] == EPS_ADMIN || $eps_user['group_id'] == EPS_MOD)
			define('IS_MODADMIN', true);
		else
			define('IS_MODADMIN', false);
		
		unset($eps_user['password']);

		// Language No Exists
		if (!@file_exists(EPS_ROOT.'lang/'.$eps_user['language']))
			$eps_user['language'] = $eps_config['default_lang'];

		// Style No Exists
		if (!@file_exists(EPS_ROOT.'style/'.$eps_user['style'].'.css'))
			$eps_user['style'] = $eps_config['default_style'];

		$eps_user['ip_address'] = get_ip();
		$eps_user['is_guest'] = false;
		
	}
	else
		set_guest();
}

// Set Guest
function set_guest()
{
	global $eps_user, $eps_config;
	$eps_user = array(); 
	$eps_user['style'] = $eps_config['default_style'];
	$eps_user['language'] = $eps_config['default_lang'];
	$eps_user['ajax'] = $eps_config['default_ajax'];
	$eps_user['timezone'] = $eps_config['default_timezone'];
	$eps_user['ip_address'] = get_ip();
	$eps_user['is_guest'] = true;

	if (!isset($_SESSION['guest']['style']))
		$_SESSION['guest']['style'] = $eps_user['style'];
	if (!isset($_SESSION['guest']['language']))
		$_SESSION['guest']['language'] = $eps_user['language'];
	if (!isset($_SESSION['guest']['ajax']))
		$_SESSION['guest']['ajax'] = $eps_user['ajax'];

	if (!defined('IS_ADMIN'))
		define('IS_ADMIN', false);
	if (!defined('IS_MODADMIN'))
	define('IS_MODADMIN', false);
}

// get $_GET variable
function eps_get_var($var_name, $default, $positive = false)
{
	if (!isset($_GET[$var_name]))
	{
		return $default;
	}

	$var = str_replace(array('\'', '"') , '', $_GET[$var_name]);
	settype($var, gettype($default));
	if ($positive && $var < 1)
	{
		return $default;
	}
	
	return $var;
}

// Get Remote IP-address
function get_ip()
{
	return $_SERVER['REMOTE_ADDR'];
}

// Check Use Ajax
function eps_use_ajax()
{
	global $eps_user;
	if (($eps_user['is_guest'] && $_SESSION['guest']['ajax']) || (!$eps_user['is_guest'] && $eps_user['ajax']))
		return true;
	else
		return false;
}

// Auto Generate Link (Normal Or Ajax) (Url, Name, Title = '', Ajax_id = '', No_rename = false, Popup = '')
// If Ajax_id == true or '', it'll use Quick AJAX method 
// if Popup == true, it show the Title as popup
function auto_gen_link($url, $name, $title = '', $ajax_id = '', $auto_normalize_url = true, $popup = false)
{
	if (eps_use_ajax())
		return gen_ajax_link($url, $name, $title, $ajax_id, $auto_normalize_url, $popup);
	else
		return gen_link($url, $name, $title, false, $popup);
}

// Generate Link (Link, Name = '', Title = '', New_window = false, Popup = '')
function gen_link($url, $name = '', $title = '', $new_window = false, $popup = false)
{
	$a = '<a href="'.$url.'"';
	if ($new_window)
		$a .= ' target="_blank"';
	if ($title != '' && !$popup)
		$a .= ' title="'.htmlspecialchars($title).'"';
	$a .= '>'.(($name != '') ? $name : $url);
	if ($title != '' && $popup)
		$a .= '<span class="popup">'.$title.'</span>';

	return $a.'</a>';
}

// Generate Ajax Link (Ajax_link, Name, Title = '')
function gen_ajax_link($url, $name, $title = '', $ajax_id = '', $auto_normalize_url = true, $popup = false)
{
	$url = str_replace('index.php?', '', $url);
	$param = 'vQ(' . "'$url'" . (($ajax_id === true || $ajax_id == '') ? '' : ",'$ajax_id'" . (($auto_normalize_url) ? ",true": '')) . ')';
	return '<a href="javascript:void(0)" onclick="' . $param . '"' . (($title != '' && !$popup) ? ' title="' . $title . '"' : '') . '>' . $name . (($title != '' && $popup) ? '<span class="popup">'.$title.'</span>' : '').'</a>';
}

// Auto Generate Form (Url, Form_id, Ajax_id, No_rename = false)
// If Ajax_id == true, it'll use Quick Javascript method 
function auto_gen_form($url, $form_id, $ajax_id = '', $auto_normalize_url = false)
{
	if (eps_use_ajax())
	{
		$url = str_replace('index.php?', '', $url);
		if ($ajax_id === true)
			return '<form id="'.$form_id.'" method="post" action="javascript:void(null)" onsubmit="vF('."'$url',this".')">';
		else if ($ajax_id != '')
			return '<form id="'.$form_id.'" method="post" action="javascript:void(null)" onsubmit="vF('."'$url',this,'$ajax_id'".(($auto_normalize_url) ? ',true': '').')">';
	}

	return '<form id="'.$form_id.'" method="post" action="'.$url.'" onsubmit="return chk_input(this)">';
}

// Generate Navigator
function gen_navlink()
{
	global $eps_config, $eps_lang, $eps_user;
	
	$always = array(
		'index' => '',
		'classlist' => 'eps=list&amp;list=k48htd1',
		'userlist' => 'eps=list&amp;list=user',
		'album' => 'eps=album'
	);
	
	if ($eps_user['is_guest'])
	{
		$extra = array(
			'login' => 'eps=login',
			'register' => 'eps=register'
		);
	}
	else
	{
		$extra = array(
			'post' => 'eps=post',
			'share' => 'eps=share',
			'profile' => 'eps=profile&amp;uid='.$eps_user['id']
		);

		if (IS_MODADMIN)
		{
			$extra['event_edit'] = 'eps=data_edit&amp;data=event&amp;var=eps_events';
			$extra['poll_edit'] = 'eps=poll_edit';
			$extra['postmark'] = 'eps=mark&amp;action=postmark';
		}

		if (IS_ADMIN)
		{
			$extra['admin'] = 'eps=admin';
			$extra['extension'] = 'eps=extension';
		}

		$extra['logout'] = 'eps=profile&amp;action=logout&amp;uid='.$eps_user['id'];
	}

	function navlink_prefix(&$item, $key)
	{
		global $eps_lang;
		$item = '<li id="nav_'.$key.'" onclick="c_nav(\''.$key.'\')">'.auto_gen_link('index.php?'.$item, $eps_lang['Nav_'.$key] , '', true);
	}
	
	array_walk($always, 'navlink_prefix');
	array_walk($extra, 'navlink_prefix');

	$links = array_merge($always, $extra);

// 	$links[] = '<li id="nav_forum">'.gen_link('forum/', $eps_lang['Nav_forum']);
	
	return '<ul>'."\n".implode('</li>'."\n", $links).'</li>'."\n".'</ul>';
}

// Generate Edit-Delete Link
function gen_editlink($url, $del = true)
{
	global $eps_lang;
	$tmp = '<li>'.auto_gen_link($url.'&amp;action=edit', $eps_lang['Edit'], $eps_lang['Edit'], true).'</li>';
	if ($del)
		$tmp .= '<li>'.auto_gen_link($url.'&amp;action=delete', $eps_lang['Delete'], $eps_lang['Delete'], true).'</li>';
	return '<ul>'.$tmp.'</ul>';
}

function gen_course_jump_tpl($base_url, $selected)
{
	global $eps_lang;
	$jump = array(
		'name' => $eps_lang['List'],
		'onchange' => (eps_use_ajax()) ? 'vQ(this.options[this.selectedIndex].value)' : 'window.location=this.options[this.selectedIndex].value',
		'value' => array(
			$base_url.'&amp;list=k48htd1' => $eps_lang['k48htd1'],
			$base_url.'&amp;list=k48htd2' => $eps_lang['k48htd2'],
			$base_url.'&amp;list=k48htd3' => $eps_lang['k48htd3'],
			$base_url.'&amp;list=k48htd4' => $eps_lang['k48htd4'],
			$base_url.'&amp;list=k48htdp' => $eps_lang['k48htdp']
		),
		'selected' => $base_url.'&amp;list='.$selected
	);
	return $jump;
}

// String Length
function eps_strlen($str)
{
	return strlen(utf8_decode($str));
}

// String To Lower
function eps_strtolower($str)
{
	if (function_exists('mb_strtolower'))
	{
		return mb_strtolower($str, mb_detect_encoding($str));
	}
	require EPS_ROOT.'include/vn_case_table.php';
	return strtr($str, $vn_upper_to_lower);
}

// String To Upper
function eps_strtoupper($str)
{
	if (function_exists('mb_strtoupper'))
	{
		return mb_strtoupper($str, mb_detect_encoding($str));
	}
	require EPS_ROOT.'include/vn_case_table.php';
	return strtr($str, array_flip($vn_upper_to_lower));
}

// SubString
function eps_substr($str, $start, $length)
{
	if (function_exists('mb_substr'))
	{
		return mb_substr($str, $start, $length, mb_detect_encoding($str));
	}
	preg_match_all('#.#su', $str, $ar); 
	return join('', array_slice($ar[0], $start, $length));
}

// Truncate
function eps_truncate($str, $len)
{
	return preg_replace('#[\s][\S]+$#u', '', eps_substr($str, 0, $len)).'... ';
}

// Line Break
function eps_linebreak($str)
{
	return str_replace("\r", "\n", str_replace("\r\n", "\n", $str));
}

// Random String
function eps_random($len, $lower = false)
{
	$chr = $lower ? 'abcdefghijklmnopqrstuvwxyz0123456789' : 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	$ran = '';
	for ($i = 0; $i < $len; $i++)
		$ran .= substr($chr, mt_rand(0, strlen($chr)-1), 1);
	return $ran;
}

// Hash !IMPORTANT
function eps_hash($str)
{
	return md5($str);
}

// Get EPS Stuff In Directories (style/language/album/thumb)
function get_stuff($stuff, $in = null)
{
	$stuffs = array();

	switch ($stuff)
	{
		// Style
		case 'style':
			$dir = dir(EPS_ROOT.'style');
			while (($entry = $dir->read()) !== false)
			{
				if (substr($entry, strlen($entry)-4) == '.css')
					$stuffs[] = substr($entry, 0, strlen($entry)-4);
			}
			break;

		// Language
		case 'language':
			$dir = dir(EPS_ROOT.'lang');
			while (($entry = $dir->read()) !== false)
			{
				if ($entry != '.' && $entry != '..' && is_dir(EPS_ROOT.'lang/'.$entry) && file_exists(EPS_ROOT.'lang/'.$entry.'/lang_common.php'))
					$stuffs[] = $entry;
			}
			break;

		// Album
		case 'album':
			$dir = dir(EPS_ALBUM_DIR);
			while (($entry = $dir->read()) !== false)
			{
				if ($entry != '.' && $entry != '..' && is_dir(EPS_ALBUM_DIR.$entry))
					$stuffs[] = $entry;
			}
			break;

		// Thumbnail
		case 'thumb':
			if (!$in)
				return null;
			$link = EPS_ALBUM_DIR.$in;
			$dir = dir($link);
			while (($entry = $dir->read()) !== false)
			{
				if ($entry != '.' && $entry != '..' && !is_dir($link.'/'.$entry) && preg_match('#_t\.(png|jpg|gif|bmp)$#',$entry))
					$stuffs[] = $entry;
			}
			break;

		default:
			return null;
	}

	$dir->close();
	if (count($stuffs) > 1)
		natsort($stuffs);
	return $stuffs;
}

// Get Extra
function get_module_result($tiny_method)
{
	global $epsclass, $eps_user, $eps_config, $eps_lang, $smarty;

	ob_start();
	require EPS_ROOT.'module.php';
	$tmp = trim(ob_get_contents());
	ob_end_clean();

	return $tmp;
}

// htmlspecialchars()
function html_clean($str)
{
	return htmlspecialchars($str);
}

// Get Picture From Thumbnail
function thumb2pic($thumbs)
{
	if (is_array($thumbs))
	{
		foreach ($thumbs as $v)
		{
			$pics[$v] = thumb2pic($v);
		}
		return $pics;
	}
	else
		return preg_replace('#_t\.(.{3})$#i', '.$1', $thumbs);
}

function pic2thumb($pics)
{
	if (is_array($pics))
	{
		foreach ($pics as $v)
		{
			$thumbs[$v] = thumb2pic($v);
		}
		return $thumbs;
	}
	else
		return preg_replace('#\.(.{3})$#i', '_t.$1', $pics);
}

// GMT Format Time
function format_time($timestamp, $date_only = false)
{
	global $eps_config, $eps_user, $eps_lang;

	$diff = $eps_user['timezone'] * 3600;
	$timestamp += $diff;
	$now = time();

	$date = gmdate($eps_config['date_format'], $timestamp);
	$today = gmdate($eps_config['date_format'], $now + $diff);
	$yesterday = gmdate($eps_config['date_format'], $now + $diff - 86400);

	if ($date == $today)
		$date = $eps_lang['Today'];
	else if ($date == $yesterday)
		$date = $eps_lang['Yesterday'];

	if (!$date_only)
		return $date.' '.gmdate($eps_config['time_format'], $timestamp);
	else
		return $date;
}

// Date string (ex. 20060321)
function date_string($timestamp, $full = true)
{
	return gmdate(($full) ? 'Ymd' : 'md', $timestamp);
}


// Generate JS Language (!!!Element name not contains "req_")
function gen_jslang($langs)
{
	global $eps_lang;

	if (empty($langs))
		return;

	$jslang = "<script type=\"text/javascript\">\n<!--\nif (typeof(jslang) == 'undefined')\n\tjslang = new Object();\njslang['required'] = '".$eps_lang['required']."';\n";

	foreach ($langs as $k => $v)
		$jslang .= "jslang['$k'] = '$v';\n";

	$jslang .= "//-->\n</script>\n";

	return $jslang;
}

// Display Alert
function gen_alert($err)
{
	global $eps_lang;
	if (empty($err))
		return;

	$tmp = '<div class="alert"><div class="alert_head">'.$eps_lang['Error'].'</div><div class="alert_body"><ul>';
	if (is_array($err))
	{
		foreach ($err as $v)
			$tmp .= '<li>'.$v.'</li>';
	}
	else
		$tmp .= '<li>'.$err.'</li>';
	$tmp .= '</ul></div></div>'."\n";
	return $tmp;
}

function gen_current_url($page = null)
{
	if ($page === true)
		$page = $_SERVER['PHP_SELF'];
	else if ($page == null)
		$page = 'index.php';
	return $page.'?'.$_SERVER['QUERY_STRING'];
}

function redirect($url, $message, $ajax_method = true)
{
	global $epsclass, $smarty, $eps_config, $eps_lang;

	if (defined('HEADER_LOADED'))
		ob_end_clean();

	if ($url == '')
		$url = 'index.php';

	$smarty->assign('tpl_redir_url', str_replace(array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $url));
	$smarty->assign('tpl_redir_title', htmlspecialchars($eps_config['title']).' | '.$eps_lang['Redirecting']);
	$smarty->assign('tpl_redir_text', $message.'<p>'.gen_link($url, $eps_lang['Click_redirect']).'</p>');

	$epsclass->db->close();
	$smarty->display(($ajax_method && eps_use_ajax()) ? 'redirect_ajax.tpl' : 'redirect.tpl');
	exit;
}

// Dir must end with slash
function create_file($content, $name, $dir)
{
	$file = ($dir === true) ? $name : $dir.$name;

	if (!file_exists($file))
	{
		@touch($file);
		@chmod($file, 0777);
		if (!file_exists($file))
			error('Unable to create File \''.$file.'\'. Permission denied', __FILE__, __LINE__);
	}

	if (!is_readable($file) || !is_writable($file))
		error('Unable to open File \''.$file.'\' for reading or writing. Permission denied', __FILE__, __LINE__);

	if (function_exists('file_put_contents'))
	{
		file_put_contents($file, $content);
	}
	else
	{
		$fo = @fopen($file, 'wb');
		if (!$fo)
			error('Unable to open File: '.$file, __FILE__, __LINE__);

		fwrite($fo, $content);
		fclose($fo);
	}
	return true;
}

function create_config_file()
{
	global $epsclass;

	// Get the forum config from the DB
	$result = $epsclass->db->query('SELECT * FROM '.TBL_CONFIG, true) or error('Unable to fetch forum config', __FILE__, __LINE__, $epsclass->db->error());
	while ($cur_config_item = $epsclass->db->fetch_row($result))
		$output[$cur_config_item[0]] = $cur_config_item[1];

	$epsclass->db->free_result($result);

	create_file('<?php'."\n".'define(\'CONFIG_LOADED\', true);'."\n".'$eps_config = '.var_export($output, true).';'."\n".'?>', FILE_CACHE_CONFIG, true);
}

// Get file_content in URL
function urlfile_get_contents($url, $show_error = true)
{
	$url_parsed = parse_url($url);
	if (empty($url_parsed['host']))
		error('Unable to get host in: '.$url, __FILE__, __LINE__);
	else
		$host = $url_parsed['host'];

	$port = (!empty($url_parsed['port'])) ? $url_parsed['port'] : 80;
	$path = (!empty($url_parsed['path'])) ? $url_parsed['path'] : '/';

	if (!empty($url_parsed['query']))
		$path .= "?".$url_parsed["query"];

	$out = "GET $path HTTP/1.0\r\nHost: $host\r\nConnection: Close\r\n\r\n";
	$fp = fsockopen($host, $port, $errno, $errstr, 30);
	if (!$fp)
	{
		if ($show_error)
			error('Unable to Open socket connection to: '.$url, __FILE__, __LINE__);
		else
			return;
	}
	else
	{
		fwrite($fp, $out);
		$body = false;
		$in = '';
		while (!feof($fp))
		{
			$s = fgets($fp, 1024);
			if ($body)
				$in .= $s;
			if ($s == "\r\n")
				$body = true;
		}
		return $in;
	}
}

// HTTP_REFERER matches $eps_config['o_base_url']/$script
function confirm_referrer($script)
{
	global $epsclass, $eps_config, $eps_lang, $eps_user;

	if (!preg_match('#^'.preg_quote(str_replace('www.', '', $eps_config['base_url']).$script, '#').'#i', str_replace('www.', '', (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''))))
		message($eps_lang['Bad referrer']);
}

// Message When In Maintenance
function maintenance_message()
{
	global $epsclass, $smarty, $eps_config, $eps_lang;

	$smarty->assign('tpl_maint_title', htmlspecialchars($eps_config['title']).' | '.$eps_lang['Maintenance']);
	$epsclass->db->close();
	$smarty->display("maintenance.tpl");
	exit;
}


// Error
function error($message, $file = '', $line = '', $db_error = false)
{
	global $eps_config, $epsclass;

	// Default Title If No $eps_config
	if (empty($eps_config))
		$eps_config['title'] = 'EPOSYS';

	// Empty output buffer and stop buffering
	if (defined('HEADER_LOADED'))
		ob_end_clean();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo htmlspecialchars($eps_config['title']) ?> / Error</title>
<style type="text/css">
<!--
body {margin: 10% 20% auto 20%; font: 10px Verdana, Arial, Helvetica}
#errorbox {border: 1px solid #b84623}
h2 {margin: 0; color: #ffffff; background-color: #b84623; font-size: 1.1em; padding: 5px 4px}
#errorbox div {padding: 6px 5px; background-color: #f1f1f1}
-->
</style>
</head>
<body>
<div id="errorbox">
	<h2>An error was encountered</h2>
	<div>
<?php
	if (defined('EPS_DEBUG'))
	{
		echo "\t\t".'<strong>File:</strong> '.$file.'<br />'."\n\t\t".'<strong>Line:</strong> '.$line.'<br /><br />'."\n\t\t".'<strong>Error reported</strong>: '.$message."\n";

		if ($db_error)
		{
			echo "\t\t".'<br /><br /><strong>Database reported:</strong> '.htmlspecialchars($db_error['error_msg']).(($db_error['error_no']) ? ' (Errno: '.$db_error['error_no'].')' : '')."\n";

			if ($db_error['error_sql'] != '')
				echo "\t\t".'<br /><br /><strong>Failed query:</strong> '.htmlspecialchars($db_error['error_sql'])."\n";
		}
	}
	else
		echo "\t\t".'Error: <strong>'.$message.'.</strong>'."\n";
?>
	</div>
</div>
</body>
</html>
<?php

	// Close Database Connection
	if ($db_error && isset($epsclass->db))
		$epsclass->db->close();

	exit;
}

// Dump Variable
function dmp()
{
	echo '<pre>';
	$num_args = func_num_args();
	for ($i = 0; $i < $num_args; $i++)
	{
		print_r(func_get_arg($i));
		echo "\n\n";
	}
	echo '</pre>';
	exit;
}
?>
