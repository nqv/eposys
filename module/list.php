<?php
/*
--------------------------------------------------------------------------------
     File:  list.php

   Module:  LIST USER/STUDENT
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2005-12-28

  Comment:
--------------------------------------------------------------------------------
*/
if (!defined('IN_EPS'))
	exit;

// GET
$p = eps_get_var('p', 1, true);
$list = eps_get_var('list', '');
$search = str_replace('*', '%', eps_get_var('search', ''));
$uid = eps_get_var('uid', 0, true);

// Paginate
$epsclass->load_class('class_paginate');
$per_page = 30;

$where_sql = '';
$list_type = 1;

// List?
switch ($list)
{
	case 'k48htd1':
	case 'k48htd2':
	case 'k48htd3':
	case 'k48htd4':
	case 'k48htdp':
		if ($search != '')
		{
			$where_sql = " WHERE name LIKE '".$epsclass->db->escape($search)."' OR s_id LIKE '".$epsclass->db->escape($search)."'";
			// For link
			$search = '&amp;search='.$search;
		}
		else
			$where_sql = " WHERE course='".$epsclass->db->escape($list)."'";

		$sqls = array(
			"SELECT COUNT(id) FROM ".TBL_K48HTD.$where_sql,
			"SELECT * FROM ".TBL_K48HTD.$where_sql
		);
		//	"SELECT name,DATE_FORMAT(birth,'%d-%m-%Y') AS bir FROM ".TBL_K48HTD.$where_sql." ORDER BY s_id"

		$page_link = 'index.php?eps=list&amp;list='.$list.$search;
		$list_type = 2;
		break;
	case 'user':
	default:
		if ($search != '')
		{
			$where_sql = " WHERE u.username LIKE '".$epsclass->db->escape($search)."'";
			$search = '&amp;search='.$search;
		}
		else if ($uid > 0)
			$where_sql = " WHERE u.id='".$epsclass->db->escape($uid)."'";		

		$sqls = array(
			"SELECT COUNT(id) FROM ".TBL_USER." u".$where_sql,
			"SELECT g.g_title,h.name,h.course,u.id,u.username,u.email,u.s_id,u.group_id,u.reg_time,u.active FROM ".TBL_USER." u LEFT JOIN ".TBL_GROUP." g ON u.group_id=g.g_id LEFT JOIN ".TBL_K48HTD." h ON u.s_id=h.s_id".$where_sql." ORDER BY u.reg_time DESC"
		);
		$page_link = 'index.php?eps=list'.$search;
}

$result = $epsclass->paginate->get_result($sqls, $page_link, $p, $per_page);
$list_shows = array();
if ($epsclass->paginate->num_result())
{
	$empty = false;
	$start = ($p - 1) * $per_page + 1;

	if ($list_type == 2)
		$smarty->assign('tpl_jump', gen_course_jump_tpl('index.php?eps=list', $list));
	
	// Show User Detail
	$details = ($list_type == 2) ? array(
		'native' => $eps_lang['Native'],
		'address' => $eps_lang['Address'],
		'phone' => $eps_lang['Phone'],
		'yahoo' => $eps_lang['Yahoo']
	) :
	array(
		'name' => $eps_lang['Name'],
		'course' => $eps_lang['Course'],
		's_id' => $eps_lang['StudentID'],
		'email' => $eps_lang['Email'],
		'active' => $eps_lang['Active']
	);

	while ($cur_user = $epsclass->db->fetch_assoc($result))
	{
		$tmp = array();

		$cur_detail = '';
		if (!$eps_user['is_guest'])
		{
			$cur_details = array();
			foreach ($details as $key => $value)
			{
				$cur_details[] = '<li><strong>'.$value.': </strong>'.$cur_user[$key];
			}
			$cur_detail = '<ul>'.implode('</li>', $cur_details).'</li></ul>';
		}

		if ($list_type == 2)
		{
			$tmp[1] = ($eps_user['is_guest']) ? html_clean($cur_user['name']) : gen_link('#', html_clean($cur_user['name']), $cur_detail, false, true);
			$tmp[2] = $cur_user['birth'];
			$tmp[3] = (!empty($eps_lang[$cur_user['course']])) ? $eps_lang[$cur_user['course']] : html_clean($cur_user['course']);
		}
		else
		{
			$link_edit = (IS_MODADMIN) ? 'index.php?eps=profile&amp;uid='.$cur_user['id'] : '#';
			$tmp[1] = ($eps_user['is_guest']) ? html_clean($cur_user['username']) : auto_gen_link($link_edit, $cur_user['username'], $cur_detail, true, true, true);
			$tmp[2] = format_time($cur_user['reg_time']);
			$tmp[3] = html_clean($cur_user['g_title']);
		}
		$tmp[0] = $start++;
		$list_shows[] = $tmp;
	}
	$smarty->assign('list_type', $list_type);
	$smarty->assign('list_shows', $list_shows);
	$smarty->assign('pagination', $epsclass->paginate->gen_page_link());
	$smarty->display('module/list.tpl');
}
else
{
	$smarty->display('empty.tpl');
}

unset($list, $search, $uid, $where_sql, $list_type, $sqls, $page_link, $list_shows, $empty, $start, $prefix, $tpl_jump);
