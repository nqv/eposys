<?php
/*
--------------------------------------------------------------------------------
     File:  w_o_t_d.php

   Module:  WORD OF THE DAY
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-03-11

  Comment:  Get From dictionary.com
--------------------------------------------------------------------------------
*/

if (!defined('IN_EPS'))
	exit;

$wotd_link = 'http://dictionary.reference.com/wordoftheday/wotd.rss';
$wotd_diff = -36000;

@include EPS_CACHE_DIR.'cache_wotd.php';
$now_string = date_string(time() + $wotd_diff);
if (!isset($wotd) || empty($wotd['desc']) || $wotd['date'] != $now_string)
{
	$epsclass->load_class('class_xml');
	$epsclass->xml->load_file($wotd_link);
	$wotd_rss = $epsclass->xml->get_rss();
	$wotd = (empty($wotd_rss)) ? array() : array(
		'date' => $now_string,
		'link' => $wotd_rss['item']['link'],
		'title' => html_clean($wotd_rss['item']['title']),
		'desc' => preg_replace('#^(.+?):#i', '<strong>$1</strong>:', html_clean($wotd_rss['item']['description']))
	);
	create_file('<?php'."\n".'$wotd = '.var_export($wotd, true).';'."\n".'?>', 'cache_wotd.php', EPS_CACHE_DIR);
}

$smarty->assign('wotd', $wotd);
$smarty->display('module/w_o_t_d.tpl');

unset($wotd_link, $wotd_diff, $now_string, $wotd_rss, $wotd);

?>
