<?php
/*
--------------------------------------------------------------------------------
     File:  login.php

   Module:  LOGIN
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-01-04

  Comment:  Addition: Forgot Password
--------------------------------------------------------------------------------
*/

if (!defined('IN_EPS'))
	exit;

// Only Unregister
if (!$eps_user['is_guest'])
{
	echo $eps_lang['Must_login'];
	return;
	//header('Location: index.php');
	//exit;
}

// GET
$action = eps_get_var('action', '');

// Visual Function
require_once EPS_ROOT.'include/function_visual.php';

// Class
$epsclass->load_class('class_validate');
$epsclass->load_class('class_antiflood');

$errors = array();
$epsclass->validate->data_reset();

// L o g i n
if (empty($action) || $action == 'login' || (isset($tiny_method) && $tiny_method == 'login'))
{
	$action = 'login';

	// Submit
	if (isset($_POST['form_sent']) && $_POST['form_sent'] == 'login')
	{
		// Clean
		$username = trim($_POST['req_username']);
		$password = trim($_POST['req_password']);
		$auto = (isset($_POST['auto'])) ? true : false;

		// Validate
		$epsclass->validate->chk_empty($username, $eps_lang['Username']);
		$epsclass->validate->chk_empty($password, $eps_lang['Password']);

		// Anti-Flood
		if (!$epsclass->antiflood->verify('login', 2))
		{
			@$confirm_code = trim($_POST['req_confirmcode']);
			if ($epsclass->validate->chk_empty($confirm_code, $eps_lang['Confirm_code']))
				$epsclass->validate->chk_match($confirm_code, eps_encrypt($_SESSION['visual'], 6), $eps_lang['Confirm_code']);
		}

		if (empty($epsclass->validate->errors))
		{
			$username_tmp = eps_strtolower($username);
			//$result = $epsclass->db->query("SELECT id,password,group_id,active FROM ".TBL_USER." WHERE LOWER(username)='".$epsclass->db->escape($username_tmp)."'") or error('Unable to fetch user info', __FILE__, __LINE__, $epsclass->db->error());
			$result = $epsclass->db->vselect(TBL_USER, array('id','password','group_id','active'), "WHERE LOWER(username)='".$epsclass->db->escape($username_tmp)."'", true);
	
			if ($epsclass->db->num_rows($result) == 1)
			{
				list($user_id, $db_password, $group_id, $active) = $epsclass->db->fetch_row($result);
				$epsclass->db->free_result($result);

				// Check
				if (!$active && !IS_ADMIN)
					$errors[] = $eps_lang['User_inactive'];
				else if ($db_password != eps_hash($password))
					$errors[] = $eps_lang['Password_wrong'];
				else
				{
					// Set User With Password In Database 
					set_user($user_id, $db_password, $auto);

					$epsclass->antiflood->update('login', 2);
					redirect('index.php'.((isset($_GET['eps']) && $_GET['eps'] != 'login') ? '?eps='.$_GET['eps'] : ''), $eps_lang['Redirect_login']);
					//redirect((isset($_SESSION['in_page']) && in_array($_GET['eps'], array('login', 'calendar', 'visual'))) ? 'index.php' : gen_current_url(), $eps_lang['Redirect_login']);
					return;
				}
			}
			else
				$errors[] = $eps_lang['Username_wrong'];;
		}
		else
		{
			$errors = $epsclass->validate->errors;
			$epsclass->validate->data_reset();
		}
	}

	$_SESSION['visual'] = eps_random(9); 
	$req_fields = array(
		'username' => $eps_lang['Username'],
		'password' => $eps_lang['Password'],
		'confirmcode' => $eps_lang['Visual_confirm'],
	);

	$smarty->assign('js_lang', gen_jslang($req_fields));
	$smarty->assign('error_show', (!empty($errors)) ? gen_alert($errors) : '');
	$smarty->assign('form_tag', auto_gen_form('index.php?eps=login', 'login', true));

	$smarty->assign('username', isset($_POST['req_username']) ? html_clean($username) : '');
	$smarty->assign('show_title', (empty($tiny_method)) ? true : false);
	$smarty->assign('size', (empty($tiny_method)) ? '30' : '15');
	$smarty->assign('antiflood_allow', $epsclass->antiflood->try_allow);
	$smarty->assign('visual', gen_visual($_SESSION['visual'], 6));
	$smarty->assign('auto', (isset($auto)) ? $auto : false);
	$smarty->assign('forgotpass_link', auto_gen_link('index.php?eps=login&amp;action=fgpass', $eps_lang['Forgot_password'], '', true));

	unset($username, $password, $confirm_code, $username_tmp, $user_id, $db_password, $group_id, $active, $errors, $auto);

	$smarty->display('module/login.tpl');
}

// F o r g o t  P a s s w o r d
else if ($action == 'fgpass')
{
	if (isset($_POST['form_sent']))
	{
		// Validate
		$email = strtolower(trim($_POST['req_email']));
		$confirm_code = trim($_POST['req_confirmcode']);

		$epsclass->validate->chk_email($email, 0, true);
		if ($epsclass->validate->chk_empty($confirm_code, $eps_lang['Confirm_code']))
			$epsclass->validate->chk_match($confirm_code, eps_encrypt($_SESSION['visual'], 6), 'Confirm_code');

		if (empty($epsclass->validate->errors))
		{
			$result = $epsclass->db->vselect(TBL_USER, array('id', 'username'), $email, 'email');
			if ($epsclass->db->num_rows($result) == 1)
			{
				list($user_id, $username) = $epsclass->db->fetch_row($result);
				$epsclass->db->free_result($result);

				// Security Code
				$security = $epsclass->validate->secr_create();
				$epsclass->db->vupdate(TBL_USER, array('security' => $security), $user_id);
				// Emailer
				$epsclass->load_class('class_emailer');

				$mail_tpl = trim(file_get_contents(EPS_ROOT.'lang/'.$eps_user['language'].'/send_password.tpl'));

				// Mail Subject
				if (preg_match('#\<subject\>(.*?)\</subject\>#is', $mail_tpl, $subject_tpl))
					$subject = $subject_tpl[1];
				else
					$subject = $eps_config['title'];

				// Mail Message
				$message = trim(preg_replace('#\<subject\>.*?\</subject\>#is', '', $mail_tpl));
				$message = str_replace('<username>', $username, $message);
				$message = str_replace('<base_url>', $eps_config['base_url'], $message);
				$message = str_replace('<activation_url>', $eps_config['base_url'].'index.php?eps=profile&action=changepass&uid='.$user_id.'&key='.$security, $message);
				$message = str_replace('<mailer>', $eps_config['title'].' - '.$eps_config['desc'], $message);

				// Send
				if ($epsclass->emailer->mail_send($email, $subject, $message))
				{
					echo '<div class="text">'.$eps_lang['Redirect_'.$action].'</div>'."\n";
					return;
				}
				else
				{
					alert($eps_lang['Mail_not_send']);
					return;
				}
			}
			else
				$errors[] = $eps_lang['Validate_no_email'];
		}
		else
		{
			$errors = $epsclass->validate->errors;
			$epsclass->validate->data_reset();
		}
	}

	$_SESSION['visual'] = eps_random(9); 
	$req_fields = array(
		'email' => $eps_lang['Email'],
		'confirmcode' => $eps_lang['Visual_confirm'],
	);
	
	$smarty->assign('js_lang', gen_jslang($req_fields));
	$smarty->assign('error_show', (!empty($errors)) ? gen_alert($errors) : '');
	$smarty->assign('form_tag', auto_gen_form('index.php?eps=login&action=fgpass', 'fgpass', true));

	$smarty->assign('visual', gen_visual($_SESSION['visual'], 6));
	
	unset($email, $confirm_code, $errors);
	$smarty->display('module/forgotpass.tpl');
}
