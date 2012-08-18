<?php
/*
--------------------------------------------------------------------------------
     File:  register.php

   Module:  REGISTER
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2005-12-31

  Comment:
--------------------------------------------------------------------------------
*/

if (!defined('IN_EPS'))
	exit;

// Only Unregistered
if (!$eps_user['is_guest'])
	return;

// Class Validate
$epsclass->load_class('class_validate');

// Class Anti-flood
$epsclass->load_class('class_antiflood');

// Visual Function
require EPS_ROOT.'include/function_visual.php';

$errors = array();
$epsclass->validate->data_reset();

// Submit
if (isset($_POST['form_sent']))
{
	// Clean
	$username = preg_replace('#\s+#s', ' ', trim($_POST['req_username']));
	$email1 = strtolower(trim($_POST['req_email1']));
	$email2 = strtolower(trim($_POST['req_email2']));
	$password1 = trim($_POST['req_password1']);
	$password2 = trim($_POST['req_password2']);
	$s_id = strtoupper(trim($_POST['req_s_id']));
	$confirm_code = trim($_POST['req_confirmcode']);
	$code = $_SESSION['visual'];
	
	if (isset($_POST['req_agree']))
	{
		// Validate
		$epsclass->validate->chk_username($username);
		if ($epsclass->validate->chk_length($password1, 6, 20, $eps_lang['Password']))
			$epsclass->validate->chk_match($password1, $password2, $eps_lang['Password']);
		if ($epsclass->validate->chk_email($email1))
			$epsclass->validate->chk_match($email1, $email2, $eps_lang['Email']);
		$epsclass->validate->chk_s_id($s_id);
		if ($epsclass->validate->chk_empty($confirm_code, $eps_lang['Confirm_code']))
			$epsclass->validate->chk_match($confirm_code, eps_encrypt($code, 6), $eps_lang['Confirm_code']);

		if (empty($epsclass->validate->errors))
		{
			$password = eps_hash($password1);
			$inserts = array(
				'username' => $username,
				'password' => $password,
				'email' => $email1,
				's_id' => $s_id,
				'group_id' => EPS_MEMBER,
				'reg_time' => time(),
				'language' => $eps_config['default_lang'],
				'style' => $eps_config['default_style'],
				'timezone' => $eps_config['default_timezone'],
				'ajax' => $eps_config['default_ajax'],
				'ip_address' => get_ip()
			);
			$epsclass->db->vinsert(TBL_USER, $inserts);
			set_user($epsclass->db->insert_id(), $password, false);
			$_SESSION['visual'] = '';

			// Emailer
			$epsclass->load_class('class_emailer');
			$mail_tpl = trim(file_get_contents(EPS_ROOT.'lang/'.$eps_user['language'].'/welcome.tpl'));
			if (preg_match('#\<subject\>(.*?)\</subject\>#is', $mail_tpl, $subject_tpl))
				$subject = $subject_tpl[1];
			else
				$subject = $eps_config['title'];
			$message = trim(preg_replace('#\<subject\>.*?\</subject\>#is', '', $mail_tpl));
			$message = str_replace('<site_title>', $eps_config['title'], $message);
			$message = str_replace('<base_url>', $eps_config['base_url'], $message);
			$message = str_replace('<username>', $username, $message);
			$message = str_replace('<password>', $password1, $message);
			$message = str_replace('<login_url>', $eps_config['base_url'].'index.php?eps=login', $message);
			$message = str_replace('<mailer>', $eps_config['title'].' - '.$eps_config['desc'], $message);
			$epsclass->emailer->mail_send($email, $subject, $message);

			redirect('index.php', $eps_lang['Redirect_'.$_GET['eps']]);
			return;
		}
		else
		{
			$errors = $epsclass->validate->errors;
			$epsclass->validate->data_reset();
		}
	}
	else
		$errors[] = $eps_lang['Validate_not_agree']; 
}

$_SESSION['visual'] = eps_random(9); 
$req_fields = array(
	'username' => $eps_lang['Username'],
	'password1' => $eps_lang['Password'],
	'password2' => $eps_lang['Password_confirm'],
	'email1' => $eps_lang['Email'],
	'email2' => $eps_lang['Email_confirm'],
	's_id' => $eps_lang['StudentID'],
	'confirmcode' => $eps_lang['Visual_confirm'],
	'agree' => $eps_lang['Rule_agreement']
);

$smarty->assign('js_lang', gen_jslang($req_fields));
$smarty->assign('error_show', (!empty($errors)) ? gen_alert($errors) : '');
$smarty->assign('form_tag', auto_gen_form('index.php?eps=register', 'register', true));

$smarty->assign('username', (!empty($username)) ? htmlspecialchars($username) : '');
$smarty->assign('email1', (!empty($email1)) ? htmlspecialchars($email1) : '');
$smarty->assign('email2', (!empty($email2)) ? htmlspecialchars($email2) : '');
$smarty->assign('s_id', (!empty($s_id)) ? htmlspecialchars($s_id) : '');

$smarty->assign('visual', gen_visual($_SESSION['visual'], 6));
$smarty->assign('rule', htmlspecialchars($eps_config['rule']));
$smarty->assign('req_agree', (isset($_POST['req_agree'])) ? true : false);

unset($req_fields, $username, $email1, $email2, $password1, $password2, $s_id, $confirm_code, $code, $errors);
$smarty->display('module/register.tpl');

?>

