<?php
/*
--------------------------------------------------------------------------------
     File:  birth.php

   Module:  HAPPY BIRTHDAY
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-04-02

  Comment:
--------------------------------------------------------------------------------
*/

if (!defined('IN_EPS'))
	exit;

$cur_time = time();
$today_str = date_string($cur_time + $eps_user['timezone'], false);
$tomorrow_str = date_string($cur_time + $eps_user['timezone'] + 86400 * 2, false);

@include EPS_CACHE_DIR.'cache_birth.php';

if (empty($eps_births) || !isset($eps_births['date']) || $eps_births['date'] != $today_str)
{
	$eps_births = array(
		'date' => $today_str,
		'today' => array(),
		'tomor' => array()
	);
	$result = $epsclass->db->query("SELECT name,DATE_FORMAT(birth,'%m%d') as date FROM ".TBL_K48HTD." WHERE DATE_FORMAT(birth,'%m%d')='$today_str' OR DATE_FORMAT(birth,'%m%d')='$tomorrow_str'") or error('Unable to fetch student\'s birthday', __FILE__, __LINE__, $epsclass->db->error());
	
	if ($epsclass->db->num_rows($result))
	{
		while ($cur_std = $epsclass->db->fetch_assoc($result))
		{
			if ($cur_std['date'] == $today_str)
				$eps_births['today'][] = $cur_std['name'];
			else
				$eps_births['tomor'][] = $cur_std['name'];
		}
	}
	$epsclass->db->free_result($result);
	create_file('<?php'."\n".'$eps_births = '.var_export($eps_births, true).';'."\n".'?>', 'cache_birth.php', EPS_CACHE_DIR);
}

$smarty->assign('birth_list', $eps_births['today']);
$smarty->assign('t_birth_list', $eps_births['tomor']);
$smarty->display('module/birth.tpl');

