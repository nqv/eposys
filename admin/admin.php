<?php
/*
--------------------------------------------------------------------------------
     File:  admin.php

   Module:  ADMIN
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-02-22

  Comment:
--------------------------------------------------------------------------------
*/

if (!defined('IN_EPS') || !IS_ADMIN)
	exit;

$updates = array();

if (isset($_POST['form_sent']) && $_POST['form_sent'] == 'eps_config')
{
	foreach ($_POST as $k => $v)
	{
		$field = preg_replace('#^req_#i', '', $k);
		eval('$updates["'.$field.'"] = eps_linebreak(trim($_POST["'.$k.'"]));');
	}

	while (list($key, $input) = @each($updates))
	{
		// Only update values that have changed
		if (array_key_exists($key, $eps_config) && $eps_config[$key] != $input)
		{
			if ($input != '' || is_int($input))
				$changes = array('value' => $input);
			else
				$changes = array('value' => NULL);
			$epsclass->db->vupdate(TBL_CONFIG, $changes, $key, 'name');
		}
	}
	create_config_file();
	redirect('index.php?eps=admin', $eps_lang['Redirect_admin']);
	return;
}

$smarty->assign('page_title', $eps_lang['Page_admin']);
$smarty->assign('error_show', (!empty($errors)) ? gen_alert($errors) : '');
$smarty->assign('form_tag', auto_gen_form('index.php?eps=admin', 'admin', true));
$smarty->assign('styles', get_stuff('style'));
$smarty->assign('languages', get_stuff('language'));
$smarty->assign('yesno_radios', array(1 => $eps_lang['Yes'], 0 => $eps_lang['No']));

foreach ($eps_config as $k => $v)
{
	eval('$smarty->assign("'.$k.'", (isset($updates["'.$k.'"])) ? $updates["'.$k.'"] : $eps_config["'.$k.'"]);');
}

unset($updates);
$smarty->display('admin/admin.tpl');

?>
