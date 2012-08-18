<?php
if (!defined('IN_EPS'))
	exit;

$smarty->assign('navlink', gen_navlink());

$eps_left = array();

// Brief news
$eps_left[] = array(
	'id' => 'brief_news',
	'head' => $eps_lang['Brief_news'],
	'content' => get_module_result('news')
);

if ($eps_config['show_poll'])
{
	$eps_left[] = array(
		'id' => 'eps_poll',
		'head' => $eps_lang['Poll'],
		'content' => get_module_result('poll')
	);
}

if ($eps_config['show_mark'])
{
	$eps_left[] = array(
		'id' => 'eps_mark',
		'head' => $eps_lang['Mark'],
		'content' => get_module_result('mark')
	);
}

$eps_left[] = array(
	'id' => 'eps_birth',
	'head' => $eps_lang['Happybirth'],
	'content' => get_module_result('birth')
);

$smarty->assign('eps_left', $eps_left);
$smarty->display('left.tpl');
?>
