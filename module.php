<?php
/*
--------------------------------------------------------------------------------
     File:  module.php

     Unit:  MODULE CALLING
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-01-13

  Comment:  Module call

--------------------------------------------------------------------------------
*/

if (!defined('COMMON_LOADED'))
{
	define('EPS_ROOT', './');
	require EPS_ROOT.'include/common.php';
}

if (defined('EPS_DEBUG'))
	$module_start_time = (float)array_sum(explode(' ', microtime()));

// Special page
if (!empty($tiny_method))
	require EPS_ROOT.'module/'.$tiny_method.'.php';
else
{

	$module = (!empty($_GET['eps'])) ? $_GET['eps'] : '';
	
	switch ($module)
	{
		case 'register':
			require EPS_ROOT.'module/register.php';
		break;
		case 'login':
			require EPS_ROOT.'module/login.php';
		break;
		case 'profile':
			require EPS_ROOT.'module/profile.php';
		break;
		case 'post':
			require EPS_ROOT.'module/post.php';
		break;
		case 'list':
			require EPS_ROOT.'module/list.php';
		break;
		case 'album':
			require EPS_ROOT.'module/album.php';
		break;
		case 'gallery':
			require EPS_ROOT.'module/gallery.php';
		break;
		case 'share':
			require EPS_ROOT.'module/share.php';
		break;
		case 'visual':
			require EPS_ROOT.'module/visual.php';
		break;
		case 'calendar':
			require EPS_ROOT.'module/calendar.php';
		break;
		case 'interface':
			require EPS_ROOT.'module/interface.php';
		break;
		case 'poll':
			require EPS_ROOT.'module/poll.php';
		break;
		case 'mark':
			require EPS_ROOT.'module/mark.php';
		break;

		case 'poll_edit':
			require EPS_ROOT.'admin/poll_edit.php';
		break;
		case 'data_edit':
			require EPS_ROOT.'admin/data_edit.php';
		break;
		case 'admin':
			require EPS_ROOT.'admin/admin.php';
		break;
		case 'extension':
			require EPS_ROOT.'admin/extension.php';
		break;

		case 'news':
		case 'index':
		default:
			require EPS_ROOT.'module/news.php';
	}
}

if (defined('EPS_DEBUG'))
{
	echo '<p class="debug">['.sprintf('%.4f', (float)array_sum(explode(' ', microtime())) - $module_start_time).' sec]</p>';
}

?>
