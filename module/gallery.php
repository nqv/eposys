<?php
/*
--------------------------------------------------------------------------------
     File:  gallery.php

   Module:  GALLERY
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-05-29

  Comment:
--------------------------------------------------------------------------------
*/

if (!defined('IN_EPS'))
	exit;

$module_path = 'index.php?eps=gallery';

$p = eps_get_var('p', 1, true);
$gid = eps_get_var('gid', 0, true);

$epsclass->load_class('class_antiflood');

// Image view
if ($gid > 1)
{
	if (isset($_POST['form_sent']) && $_POST['form_sent'] == 'gallery_cm')
	{
		return;
	}
	
	$result = $epsclass->db->vselect(TBL_GALLERY, true, $gid);
// 	$sql = $db->vselect(TBL_GALLERY);
}
else
{
	if (isset($_POST['form_sent']) && $_POST['form_sent'] == 'gallery')
	{
		$epsclass->load_class('class_validate');
		$epsclass->validate->data_reset();

		$description = trim($_POST['req_description']);
		if ($epsclass->validate->chk_empty($description, $eps_lang['Description']))
		{
			$epsclass->load_class('class_upload', EPS_GALLERY_DIR, array('jpg', 'gif', 'png'), 1512000);
			if ($epsclass->upload->up('req_file'))
			{
				$epsclass->load_class('class_image');
				$epsclass->image->create_thumb(EPS_GALLERY_DIR . $epsclass->upload->uploaded_infos['name']);
				$inserts = array(
					'filename' => $epsclass->upload->uploaded_infos['name'],
					'filesize' => $epsclass->upload->uploaded_infos['size'],
					'description' => $description,
					'poster_id' => $eps_user['id'],
					'posted' => time()
				);
				$epsclass->db->vinsert(TBL_GALLERY, $inserts);
// 					redirect('index.php?eps=share', $eps_lang['Redirect_share_post']);
				return;
			}
			else
				$errors = $epsclass->upload->errors;
		}
		else
			$errors = $epsclass->validate->errors;
	}

	$epsclass->load_class('class_paginate');
	$sqls = array(
		"SELECT COUNT(*) FROM ".TBL_GALLERY,
		"SELECT u.username,g.* FROM ".TBL_GALLERY." g LEFT JOIN ".TBL_USER." u ON g.poster_id=u.id ORDER BY g.posted DESC",
	);
	$result = $epsclass->paginate->get_result($sqls, $module_path, $p);

	$gallery_shows = array();
	while ($cur_gallery = $epsclass->db->fetch_assoc($result))
	{
		$gallery_shows[] = auto_gen_link(
			$module_path . '&amp;gid=' . $cur_gallery['id'],
			'<img src="' . EPS_GALLERY_DIR . pic2thumb($cur_gallery['filename']) . '" />',
			'<b>' . html_clean($cur_gallery['username']) . '</b> - <i>' . format_time($cur_gallery['posted']) . ' (' . floor($cur_gallery['filesize'] / 1024) . ' KB)</i><br />' . html_clean($cur_gallery['description']),
			true,
			true,
			true
		);
	}

	$smarty->assign('error_show', (!empty($errors)) ? gen_alert($errors) : '');
	$smarty->assign('description', (isset($description)) ? html_clean($description) : '');
	$smarty->assign('p', $p);
	$smarty->assign('gallery_dir', EPS_GALLERY_DIR);
	$smarty->assign('gallery_shows', $gallery_shows);
	$smarty->assign('pagination', $epsclass->paginate->gen_page_link());
}
$smarty->display('module/gallery.tpl');
?>
