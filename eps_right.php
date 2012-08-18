<?php
if (!defined('IN_EPS'))
	exit;

$eps_right = array();

// Calendar
$eps_right[] = array(
	'id' => 'eps_calendar',
	'head' => $eps_lang['Calendar'],
	'content_id' => 'calendar_content',
	'content' => get_module_result('calendar')
);

$eps_right[] = array(
	'id' => 'wotd',
	'head' => 'Word Of The Day',
	'content' => get_module_result('w_o_t_d')
);

// Forum Topic
$eps_right[] = array(
	'id' => 'forum_topic',
	'head' => $eps_lang['Forum_topic'],
	'content' => get_module_result('punbb_topic')
);

// Login
if ($eps_user['is_guest'] && !(isset($_GET['eps']) && (in_array($_GET['eps'], array('login', 'register', 'profile')))))
{
	$eps_right[] = array(
		'id' => 'tiny_login',
		'head' => $eps_lang['Login'],
		'content' => get_module_result('login')
	);
}

// Interface
if ($eps_user['is_guest'])
{
	$eps_right[] = array(
		'id' => 'guest_interface',
		'head' => $eps_lang['Interface'],
		'content' => get_module_result('interface')
	);
}

$smarty->assign('eps_right', $eps_right);
$smarty->display('right.tpl');
?>
