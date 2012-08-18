<?php
/*
--------------------------------------------------------------------------------
     File:  download.php

   Module:  DOWNLOAD
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-03-12

  Comment:
--------------------------------------------------------------------------------
*/

define('EPS_ROOT', './');
require EPS_ROOT.'include/common.php';

// No Guest
if ($eps_user['is_guest'])
	return;

$down = (!empty($_GET['down'])) ? $_GET['down'] : '';
$id = (!empty($_GET['id']) && intval($_GET['id']) >= 1) ? intval($_GET['id']) : '';

if (empty($id))
	return;

if ($down == 'share')
{
	$result = $epsclass->db->vselect(TBL_SHARE, array('url','size','mimetype'), $id);
	if (!$epsclass->db->num_rows($result))
	{
		$smarty->assign('tpl_eps_title', $eps_lang['Error']);
		$smarty->assign('tpl_eps_main', gen_alert($eps_lang['Bad_request']));
		$smarty->display('main.tpl');
		return;
	}
	else
	{
		$this_share = $epsclass->db->fetch_assoc($result);
		$epsclass->db->free_result($result);
		$this_share['full_url'] = EPS_SHARE_DIR.$this_share['url'];
		if (!is_file($this_share['full_url']))
		{
			alert($eps_lang['Bad_request']);
			return;
		}
		else
		{
			// Send Header
			//header('Content-Type: '.$this_share['mimetype']);
			header('Content-type: application/force-download');
			header('Content-Disposition: attachment; filename="'.basename($this_share['url']).'"');
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
}

?>
