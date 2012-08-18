<?php
/*
--------------------------------------------------------------------------------
     File:  poll.php

   Module:  POLL
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-03-04

  Comment:
--------------------------------------------------------------------------------
*/

// if (!(IS_ADMIN || IS_MOD))
// 	exit;

$poll_content = (is_file(FILE_POLL_DATA)) ? str_replace("\r", "", trim(file_get_contents(FILE_POLL_DATA))) : '';

if (isset($_POST['form_sent']))
{
	$poll_content = str_replace("\r", "", trim($_POST['poll_content']));
	create_file($poll_content, FILE_POLL_DATA, true);
	if (isset($_POST['reset']))
	{
		unlink(EPS_POLL_DIR.'polled_ip');
		unlink(EPS_POLL_DIR.'polled_id');
	}
	redirect('index.php?eps='.(isset($_GET['eps']) ? $_GET['eps'] : ''), $eps_lang['Redirect_data_edit']);
}
$smarty->assign('form_tag', auto_gen_form('index.php?eps=poll_edit', 'poll_edit', true));
$smarty->assign('poll_content', $poll_content);

unset($poll_content);
$smarty->display('admin/poll_edit.tpl');
