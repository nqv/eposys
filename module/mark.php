<?php
/*
--------------------------------------------------------------------------------
     File:  mark.php

   Module:  MARK
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-03-12

  Comment:
--------------------------------------------------------------------------------
*/
if (!defined('IN_EPS'))
	exit;

if ($eps_user['is_guest'])
{
	echo $eps_lang['Must_login'];
	return;
}

$action = eps_get_var('action', '');
$course = eps_get_var('list', 'k48htd1');

$eps_subjects = array(
	'm1' => 4,	// DTCS
	'm2' => 3,	// DTS
	'm3' => 4,	// Luoi
	'm4' => 4,	// CCD
	'm5' => 5,	// VXL
	'm6' => 2,	// AT
	'm7' => 2,	// DKTD
	'm7' => 4	// MD
);
$eps_course = array('k48htd1', 'k48htd2', 'k48htd3', 'k48htd4', 'k48htdp');

if ($action == 'postmark')
{
	if (!in_array($course, $eps_course))
		return;

	if (isset($_POST['form_sent']) && $_POST['form_sent'] == 'mark')
	{
		$do_update = false;
		foreach ($eps_subjects as $cur_sbj => $sbj_v)
		{
			if (isset($_POST['c_'.$cur_sbj]))
			{
				$cur_sbj_ids = $_POST[$cur_sbj];
				$cur_sbj_marks = array();
				foreach ($cur_sbj_ids as $k => $v)
					$cur_sbj_marks[($v == '') ? 'null' : intval($v)][] = $k;

				foreach ($cur_sbj_marks as $k => $v)
					$epsclass->db->query("UPDATE ".TBL_K48MARK." SET $cur_sbj=$k WHERE `id` IN (".implode(',', $v).");") or error('Unable to update student marks', __FILE__, __LINE__, $epsclass->db->error());
				$do_update = true;
			}
		}
		
		if ($do_update)
		{
			redirect('index.php?eps=mark&amp;action=postmark&amp;list='.$course, $eps_lang['Redirect_mark']);
			return;
		}
		else
		{
			$errors = $eps_lang['No_mark_subject'];
		}
	}

	$result = $epsclass->db->query("SELECT h.name, m.* FROM ".TBL_K48MARK." m LEFT JOIN ".TBL_K48HTD." h ON h.s_id=m.s_id WHERE h.course='".$epsclass->db->escape($course)."'") or error('Unable to fetch mark', __FILE__, __LINE__, $epsclass->db->error());
	if (!$epsclass->db->num_rows($result))
	{
		alert($eps_lang['Bad_request']);
		return;
	}

	$students = array();
	while ($cur_std = $epsclass->db->fetch_assoc($result))
	{
		$tmp = array(
			'id' => $cur_std['id'],
			's_id' => $cur_std['s_id'],
			'name' => $cur_std['name']
		);
		foreach ($eps_subjects as $k => $v)
			$tmp[$k] = $cur_std[$k];

		$students[] = $tmp;
	}
	$epsclass->db->free_result($result);

	$smarty->assign('tpl_jump', gen_course_jump_tpl('index.php?eps=mark&amp;action=postmark', $course));
	$smarty->assign('error_show', (!empty($errors)) ? gen_alert($errors) : '');
	$smarty->assign('form_tag', auto_gen_form('index.php?eps=mark&amp;action='.$action.'&amp;list='.$course, 'mark', true));
	$smarty->assign('subjects', $eps_subjects);
	$smarty->assign('students', $students);
	$smarty->display('module/mark.tpl');
}
else
{
	$result = $epsclass->db->query("SELECT * FROM ".TBL_K48MARK." WHERE s_id='".$epsclass->db->escape($eps_user['s_id'])."'") or error('Unable to fetch user mark', __FILE__, __LINE__, $epsclass->db->error());
	if ($epsclass->db->num_rows($result) != 1)
		return;

	$marks = array_slice($epsclass->db->fetch_assoc($result), 2);
	$epsclass->db->free_result($result);

	$mark_sum = $sum = null;
	foreach ($marks as $k => $v)
	{
		if ($v != null)
		{
			$mark_sum += $v * $eps_subjects[$k];
			$sum += $eps_subjects[$k];
		}
	}
	//$average = floor($average / array_sum($eps_subjects) * 100) / 100;

	$smarty->assign('marks', $marks);
	$smarty->assign('average', ($sum) ? floor($mark_sum / $sum * 100) / 100 : '');
	$smarty->display('module/mark_show.tpl');
}

?>
