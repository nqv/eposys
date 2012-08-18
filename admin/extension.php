<?php
/*
--------------------------------------------------------------------------------
     File:  extension.php

   Module:  EXTENSION
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-03-14

  Comment:
--------------------------------------------------------------------------------
*/

if (!defined('IN_EPS') || !IS_ADMIN)
	exit;

if (isset($_POST['form_sent']))
{
 	if ($_POST['form_sent'] == 'archive')
 	{
		if (!empty($_POST['req_unpack_file']) && !empty($_POST['req_unpack_dest']))
		{
			$epsclass->load_class('class_archive');
	
			$unpack_file = $_POST['req_unpack_file'];
			$unpack_dest = $_POST['req_unpack_dest'];
	
			if ($epsclass->archive->extract($unpack_file, $unpack_dest) == 0)
				$errors = 'Can\'t extract file "'.$unpack_file.'" to "'.$unpack_dest.'". PCLZip says: '.$epsclass->archive->errorInfo();
			else
			{
				//redirect('index.php?eps=admin', 'File is un-packed');
				echo 'File "'.$unpack_file.'" is un-packed to "'.$unpack_dest.'"';
				return;
			}
		}
		else
			$errors = 'No File or Destination to extract.';
	}
	else if ($_POST['form_sent'] == 'backup')
	{
		$task = intval($_POST['task']);
		$epsclass->load_class('class_mysql_backup');
		if ($epsclass->mysql_backup->backup($task))
		{
			echo 'Database backup-file is created.';
			return;
		}
		else
			$errors = $epsclass->mysql_backup->error;
	}
}

$smarty->assign('error_show', (!empty($errors)) ? gen_alert($errors) : '');
$smarty->assign('form_tag', auto_gen_form('index.php?eps=extension', 'admin', true));

$smarty->assign('unpack_file', isset($unpack_file) ? $unpack_file : '');
$smarty->assign('unpack_dest', isset($unpack_dest) ? $unpack_dest : '');

$smarty->assign('task_radios', array('1' => 'Create backup-file on Server', '2' => 'Download backup-file'));
$smarty->assign('task', (isset($task)) ? $task : '1');

$smarty->assign('bigdump_link', 'include/bigdump/bigdump.php');
$smarty->display('admin/extension.tpl');
?>
