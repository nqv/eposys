<?php
/*
--------------------------------------------------------------------------------
     File:  share.php

   Module:  SHARE
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-01-23

  Comment:
--------------------------------------------------------------------------------
*/
if (!defined('IN_EPS'))
	exit;

// No Guest
if ($eps_user['is_guest'])
{
	echo gen_alert($eps_lang['Must_login']);
	return;
}

// Class Validate
$epsclass->load_class('class_validate');
$epsclass->load_class('class_antiflood');
$allow_exts = explode(',', $eps_config['upload_allowed']);
$epsclass->load_class('class_upload', EPS_SHARE_DIR, $allow_exts, $eps_config['max_size_upload']);

// GET
$p = eps_get_var('p', 1, true);
$shid = eps_get_var('shid', 0, true);
$action = eps_get_var('action', '');

if (!in_array($action, array('download', 'edit', 'delete')))
	$action = '';

$errors = array();
$epsclass->validate->data_reset();

$req_fields = array(
	'comment' => $eps_lang['Comment'],
	'file' => $eps_lang['File']
);

$smarty->assign('p', $p);
$smarty->assign('js_lang', gen_jslang($req_fields));
$smarty->assign('shid', $shid);
$smarty->assign('action', $action);

// D o w n l o a d  O r  E d i t
if ($shid)
{
	// Fetch
	//$result = $epsclass->db->query("SELECT * FROM ".TBL_SHARE." WHERE id='".$epsclass->db->escape($shid)."'") or error('Unable to fetch post', __FILE__, __LINE__, $epsclass->db->error());
	$result = $epsclass->db->vselect(TBL_SHARE, true, $shid);
	if (!$epsclass->db->num_rows($result))
	{
		alert($eps_lang['Bad_request']);
		return;
	}
	else
	{
		$this_share = $epsclass->db->fetch_assoc($result);
		$this_share['full_url'] = EPS_SHARE_DIR.$this_share['url'];
		$this_share['file_name'] = basename($this_share['url']);
	}
	$epsclass->db->free_result($result);

	if ($action == 'edit' || $action == 'delete')
	{
		// Check Author
		if (!IS_MODADMIN && $this_share['poster_id'] != $eps_user['id'])
		{
			alert($eps_lang['No_permision']);
			return;
		}
	}
	else if ($action == 'download')
	{
		//$fp = fopen($this_share['full_url'], "rb");
		if (!is_file($this_share['full_url']))
		{
			alert($eps_lang['Bad_request']);
			return;
		}
		else
		{
			ob_clean();
			// Send Header
			header('Content-Disposition: attachment; filename='.rawurlencode($this_share['url']));
			//header('Content-Type: '.$this_share['mimetype']);
			header('Content-type: application/octetstream');
			if($this_share['size'] != 0)
				header('Content-Length: '.$this_share['size']);
			header('Content-Transfer-Encoding: binary');
			header('Pragma: no-cache');
			header('Expires: 0');
			header('Connection: close');
			readfile($this_share['full_url']);
			exit();
		}
	}

	// Post
	if (isset($_POST['form_sent']))
	{
		// Form Correct?
		if (($_POST['form_user_id'] != $eps_user['id'] && !IS_MODADMIN) || !in_array($action, array('edit', 'delete')))
		{
			alert($eps_lang['Bad_request']);
			return;
		}
		// If Delete
		else if ($action == 'delete')
		{
			unlink($this_share['full_url']);
			$epsclass->db->vdelete(TBL_SHARE, $shid);
			redirect('index.php?eps=share', $eps_lang['Redirect_share_'.$action]);
			return;
		}
	
		// Anti-Flood
		else if (!$epsclass->antiflood->verify('share'))
			$errors[] = $eps_lang['Flood_remain'].' '.$epsclass->antiflood->wait.' '.$eps_lang['Second'];

		// Clean
		else
		{
			$comment = trim($_POST['req_comment']);
			$epsclass->validate->chk_empty($comment, $eps_lang['Comment']);
		}
		// Process
		if (empty($errors))
		{
			if (empty($epsclass->validate->errors))
			{
				$updates = array(
					'comment' => $comment
				);

				if (isset($_FILES['change_file']['name']) && $_FILES['change_file']['name'] != '')
				{
					$backup_file = $this_share['full_url'].'.bak';
					rename($this_share['full_url'], $backup_file);
					if ($epsclass->upload->up('change_file'))
					{
						$updates['url'] = $epsclass->upload->uploaded_infos['name'];
						$updates['size'] = $epsclass->upload->uploaded_infos['size'];
						$updates['mimetype'] = $epsclass->upload->uploaded_infos['type'];
						$updates['post_time'] = time();

						unlink($backup_file);

						$epsclass->db->vupdate(TBL_SHARE, $updates, $shid);
						redirect('index.php?eps=share', $eps_lang['Redirect_share_'.$action]);
						return;
					}
					else
					{
						$errors = $epsclass->upload->errors;
						rename($backup_file, $this_share['full_url']);
					}
				}
				else
				{
					$epsclass->db->vupdate(TBL_SHARE, $updates, $shid);
					redirect('index.php?eps=share', $eps_lang['Redirect_share_'.$action]);
					return;
				}
			}
			else
			{
				$errors = $epsclass->validate->errors;
				$epsclass->validate->data_reset();		
			}
		}
	}
	$smarty->assign('error_show', (!empty($errors)) ? gen_alert($errors) : '');
	$smarty->assign('share_name', html_clean($this_share['file_name']));
	$smarty->assign('share_post_time', format_time($this_share['post_time']));
	$smarty->assign('comment', html_clean(isset($comment) ? $comment : $this_share['comment']));
	$smarty->display('module/share_edit.tpl');
}

// U p l o a d
else
{
	// Submit
	if (isset($_POST['form_sent']))
	{
		$comment = trim($_POST['req_comment']);
		if ($epsclass->validate->chk_empty($comment, $eps_lang['Comment']))
		{
			if ($epsclass->upload->up('req_file'))
			{
				$inserts = array(
					'url' => $epsclass->upload->uploaded_infos['name'],
					'comment' => $comment,
					'size' => $epsclass->upload->uploaded_infos['size'],
					'mimetype' => $epsclass->upload->uploaded_infos['type'],
					'poster_id' => $eps_user['id'],
					'post_time' => time()
				);
				$epsclass->db->vinsert(TBL_SHARE, $inserts);
				redirect('index.php?eps=share', $eps_lang['Redirect_share_post']);
				return;
			}
			else
				$errors = $epsclass->upload->errors;
		}
		else
			$errors = $epsclass->validate->errors;
	}

	// Paginate
	$epsclass->load_class('class_paginate');

	$sqls = array(
		"SELECT COUNT(id) FROM ".TBL_SHARE,
		"SELECT u.username,s.* FROM ".TBL_SHARE." s LEFT JOIN ".TBL_USER." u ON s.poster_id=u.id ORDER BY s.post_time DESC",
	);
	$result = $epsclass->paginate->get_result($sqls, 'index.php?eps=share', $p);

	$shares = array();
	if ($epsclass->paginate->num_result())
	{
		while ($cur_share = $epsclass->db->fetch_assoc($result))
		{
			$tmp = array(
				'link' => gen_link('download.php?down=share&amp;id='.$cur_share['id'], html_clean($cur_share['url']), '', true),
				'comment' => html_clean($cur_share['comment']),
				'poster' => auto_gen_link('index.php?eps=list&amp;list=user&amp;uid='.$cur_share['poster_id'], html_clean($cur_share['username']), '', true),
				'post_time' => format_time($cur_share['post_time'])
			);

			if ($cur_share['poster_id'] == $eps_user['id'] || IS_MODADMIN)
				$tmp['editlink'] = gen_editlink('index.php?eps=share&amp;shid='.$cur_share['id']);
			
			$shares[] = $tmp;
		}
		$smarty->assign('pagination', $epsclass->paginate->gen_page_link());
	}

	$smarty->assign('error_show', (!empty($errors)) ? gen_alert($errors) : '');
	$smarty->assign('shares', $shares);
	$smarty->assign('comment', (isset($comment)) ? html_clean($comment) : '');
	$smarty->display('module/share.tpl');
}

unset($p, $shid, $action, $errors, $req_fields, $this_share, $comment, $updates, $inserts, $sqls, $shares);
?>
