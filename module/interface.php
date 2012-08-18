<?php
/*
--------------------------------------------------------------------------------
     File:  interface.php

   Module:  INTERFACE
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-02-11

  Comment:  Change Interface for Guest
--------------------------------------------------------------------------------
*/

if (!defined('IN_EPS'))
	exit;

if (!$eps_user['is_guest'])
	return;

// Class Anti-flood
$epsclass->load_class('class_antiflood');

// Submit
if (isset($_POST['form_sent']) && $_POST['form_sent'] == 'interface')
{
	$redirect_to = (!empty($_POST['redirect_to'])) ? '?eps='.$_POST['redirect_to'] : '';
	$_SESSION['guest']['style'] = trim($_POST['guest_style']);
	$_SESSION['guest']['language'] = trim($_POST['guest_language']);
	$_SESSION['guest']['ajax'] = isset($_POST['guest_ajax']) ? 1 : 0;
	redirect('index.php'.$redirect_to, $eps_lang['Redirect_interface']);
	return;
}

$smarty->assign('styles', get_stuff('style'));
$smarty->assign('languages', get_stuff('language'));
$smarty->assign('cur_guest_style', $_SESSION['guest']['style']);
$smarty->assign('cur_guest_language', $_SESSION['guest']['style']);
$smarty->assign('cur_guest_ajax', $_SESSION['guest']['ajax']);
$smarty->assign('redirect_to', (isset($_GET['eps'])) ? $_GET['eps'] : '');

$smarty->display('module/interface.tpl');

?>
