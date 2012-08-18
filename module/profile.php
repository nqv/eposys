<?php
/*
--------------------------------------------------------------------------------
     File:  profile.php

   Module:  PROFILE
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-01-15

  Comment:  Addition: Logout, Change Password
--------------------------------------------------------------------------------
*/

if (!defined('IN_EPS'))
	exit;

if ($eps_user['is_guest'])
{
	echo gen_alert($eps_lang['Must_login']);
	return;
}

// GET
$uid = eps_get_var('uid', $eps_user['id'], true);
$action = eps_get_var('action', '');

// Class Validate
$epsclass->load_class('class_validate');

// Class Anti-flood
$epsclass->load_class('class_antiflood');

$tabindex = 1;
$errors = array();
$need_old_pass = true;
$epsclass->validate->data_reset();

$smarty->assign('uid', $uid);

// L o g o u t
if ($action == 'logout')
{
	if ($uid != $eps_user['id'])
		return;
	else
	{
		set_user(0, 0);
		redirect('index.php', $eps_lang['Redirect_'.$action]);
		return;
	}
}

// C h a n g e  P a s s w o r d
else if ($action == 'changepass')
{
	$secr_key = eps_get_var('key', '');
	// Permission
	if (!empty($secr_key) || IS_ADMIN)
	{
		if (!IS_ADMIN)
		{
			if ($epsclass->validate->secr_verify($secr_key))
			{
				//$result = $epsclass->db->query("SELECT 1 FROM ".TBL_USER." WHERE id='".$epsclass->db->escape($uid)."' AND security='".$epsclass->db->escape($secr_key)."'") or error('Unable to fetch code', __FILE__, __LINE__, $epsclass->db->error());
				$result = $epsclass->db->vselect(TBL_USER, "1", "WHERE `id`='".$epsclass->db->escape($uid)."' AND `security`='".$epsclass->db->escape($secr_key)."'", true);
				if ($epsclass->db->num_rows($result) == 1)
					$need_old_pass = false;	// Forgot Password Case
				else
				{
					alert($eps_lang['Bad_request']);
					return;
				}
			}
			else
			{
				alert($epsclass->validate->errors);
				return;
			}
		}
		else if ($uid != $eps_user['id'])
		{
			$need_old_pass = false;
		}
	}
	else if ($eps_user['is_guest'] || $uid != $eps_user['id'])
	{
		return;
	}

	// Submit
	if (isset($_POST['form_sent']))
	{
		// Check Form
		if ($uid != $_POST['profile_id'] && !IS_ADMIN)
			return;

		if ($need_old_pass)
		{
			$old_password = trim($_POST['req_old_password']);
			$epsclass->validate->chk_empty($old_password, $eps_lang['Old_password']);
		}
		$new_password1 = trim($_POST['req_new_password1']);
		$new_password2 = trim($_POST['req_new_password2']);
		$epsclass->validate->chk_length($new_password1, 6, 20, $eps_lang['New_password']);
		$epsclass->validate->chk_match($new_password1, $new_password2, $eps_lang['New_password']);

		if (empty($epsclass->validate->errors))
		{
			if ($need_old_pass)
			{
				// Fetch Password
				$result = $epsclass->db->vselect(TBL_USER, true, $uid);

				if (!$epsclass->db->num_rows($result))
					return;
				else
					$password = $epsclass->db->result($result);

				$epsclass->db->free_result($result);
				$epsclass->validate->chk_match(eps_hash($old_password), $password, $eps_lang['Old_password']);
			}

			if (empty($epsclass->validate->errors))
			{
				// Update
				$updates = array(
					'password' => eps_hash($new_password1),
					'security' => ''
				);
				$epsclass->db->vupdate(TBL_USER, $updates, $uid);
				redirect('index.php?eps=profile&amp;uid='.$uid, $eps_lang['Redirect_pass_change']);
				return;
			}
			else
			{
				$errors = $epsclass->validate->errors;
				$epsclass->validate->data_reset();
			}
		}
		else
		{
			$errors = $epsclass->validate->errors;
			$epsclass->validate->data_reset();
		}
	}
	$req_fields = array(
		'old_password' => $eps_lang['Old_password'],
		'new_password1' => $eps_lang['New_password'],
		'new_password2' => $eps_lang['New_password_confirm']
	);
	
	$smarty->assign('js_lang', gen_jslang($req_fields));
	$smarty->assign('error_show', (!empty($errors)) ? gen_alert($errors) : '');
	$smarty->assign('form_tag', auto_gen_form('index.php?eps=profile&amp;action=changepass&amp;uid='.$uid.((!empty($secr_key)) ? '&amp;key='.html_clean($secr_key) : ''), 'changepass', true));
	$smarty->assign('need_old_pass', $need_old_pass);

	$smarty->display('module/changepass.tpl');
}

// E d i t  P r o f i l e
else
{
	$action = 'edit';

	if ($eps_user['is_guest'])
	{
		header('Location: index.php');
		exit;
	}

	// Fetch User Info
	$result = $epsclass->db->query("SELECT g.g_title, h.name,h.birth,h.course,h.native,h.address,h.phone,h.yahoo, u.id,u.username,u.email,u.s_id,u.group_id,u.reg_time,u.language,u.style,u.ajax,u.active FROM ".TBL_USER." u LEFT JOIN ".TBL_GROUP." g ON g.g_id=u.group_id LEFT JOIN ".TBL_K48HTD." h ON u.s_id=h.s_id WHERE u.id='".$epsclass->db->escape($uid)."'") or error('Unable to fetch user', __FILE__, __LINE__, $epsclass->db->error());
	if (!$epsclass->db->num_rows($result))
		return;
	else
		$user = $epsclass->db->fetch_assoc($result);
	$epsclass->db->free_result($result);

	// Check User
	if ((!IS_MODADMIN && $uid != $eps_user['id']) || $eps_user['group_id'] > $user['group_id'])
		return;

	// Submit
	if (isset($_POST['form_sent']))
	{
		// Check Form
		if ($uid != $_POST['profile_id'] && !IS_ADMIN)
			return;
	
		if ($_POST['form_sent'] == 1)
		{
			// Clean & Validate
			if (IS_ADMIN || (IS_MODADMIN && $eps_user['id'] != $user['id']))
			{
				$group_id = intval(trim($_POST['group_id']));
				$active = trim($_POST['active']);
				if (IS_ADMIN)
				{
					$username = preg_replace('#\s+#s', ' ', trim($_POST['req_username']));
					$epsclass->validate->chk_username($username, $uid);
				}
				else if ($group_id <= $eps_user['group_id'])
					return;
			}

			$email = strtolower(trim($_POST['req_email']));
			$s_id = strtoupper(trim($_POST['req_s_id']));
			$style = trim($_POST['style']);
			$language = trim($_POST['language']);
			$use_ajax = isset($_POST['use_ajax']) ? 1 : 0;

			$epsclass->validate->chk_email($email, $uid);
			$epsclass->validate->chk_s_id($s_id, $uid);

			if (empty($epsclass->validate->errors))
			{
				$updates = array(
					'email' => $email,
					's_id' => $s_id,
					'style' => $style,
					'language' => $language,
					'ajax' => $use_ajax
				);
				
				if (IS_ADMIN || (IS_MODADMIN && $eps_user['id'] != $user['id']))
				{
					$updates['group_id'] = $group_id;
					if (IS_ADMIN)
						$updates['username'] = $username;
				}
				$epsclass->db->vupdate(TBL_USER, $updates, $uid);
				redirect('index.php?eps=profile&amp;uid='.$uid, $eps_lang['Redirect_profile_change']);
				return;
			}
			else
			{
				$errors = $epsclass->validate->errors;
				$epsclass->validate->data_reset();
			}
		}
		else if ($_POST['form_sent'] == 2)
		{
			$eps_s_id = trim($_POST['eps_s_id']);
			
			if (!empty($eps_s_id))
			{
				$native = trim($_POST['native']);
				$address = trim($_POST['address']);
				$phone = trim($_POST['phone']);
				$yahoo = trim($_POST['yahoo']);
	
				$result = $epsclass->db->query("SELECT 1 FROM ".TBL_K48HTD." h INNER JOIN ".TBL_USER." u ON (h.s_id=u.s_id AND u.id='".$epsclass->db->escape($uid)."') WHERE h.s_id='".$epsclass->db->escape($eps_s_id)."'") or error('Unable to fetch StudentID', __FILE__, __LINE__, $epsclass->db->error());
				if ($epsclass->db->num_rows($result) == 1)
				{
					$updates = array(
						'native' => $native,
						'address' => $address,
						'phone' => $phone,
						'yahoo' => $yahoo
					);
					$epsclass->db->vupdate(TBL_K48HTD, $updates, $eps_s_id, 's_id');
					redirect('index.php?eps=profile&amp;uid='.$uid, $eps_lang['Redirect_profile_change']);
					return;
				}
				else
					$errors = $eps_lang['Validate_no_StudentID_found'];				
			}
			else
				return;
		}
		else
			return;	
	}

	$req_fields = array(
		'username' => $eps_lang['Username'],
		'email' => $eps_lang['Email'],
		's_id' => $eps_lang['StudentID']
	);

	$groups = array();
	if (IS_ADMIN || (IS_MODADMIN && $eps_user['id'] != $user['id']))
	{
		$allow_group_id = (IS_ADMIN) ? EPS_ADMIN : EPS_MOD + 1;
		$result = $epsclass->db->query('SELECT g_id, g_title FROM '.TBL_GROUP.' WHERE g_id!='.EPS_GUEST.' AND g_id>='.$allow_group_id.' ORDER BY g_id') or error('Unable to fetch user group list', __FILE__, __LINE__, $epsclass->db->error());
		while ($cur_group = $epsclass->db->fetch_assoc($result))
		{
			$groups[$cur_group['g_id']] = html_clean($cur_group['g_title']);
		}
		$epsclass->db->free_result($result);
	}
	else
		$user['g_title'] = html_clean($user['g_title']);

	// Parse
	$user['reg_time'] = format_time($user['reg_time']);
	$user['name'] = html_clean($user['name']);
	$user['birth'] = html_clean($user['birth']);
	$user['course'] = (!empty($eps_lang[$user['course']])) ? $eps_lang[$user['course']] : html_clean($user['course']);
	$user['phone'] = html_clean((isset($phone)) ? $phone : $user['phone']);
	$user['yahoo'] = html_clean((isset($yahoo)) ? $yahoo : $user['yahoo']);

	$smarty->assign('yesno_radios', array(1 => $eps_lang['Yes'], 0 => $eps_lang['No']));

	$smarty->assign('is_admin', IS_ADMIN);
	$smarty->assign('is_modadmin', IS_MODADMIN);

	$smarty->assign('user', $user);
	$smarty->assign('js_lang', gen_jslang($req_fields));
	$smarty->assign('error_show', (!empty($errors)) ? gen_alert($errors) : '');
	$smarty->assign('form_tag1', auto_gen_form('index.php?eps=profile&amp;uid='.$uid, 'profile', true));
	$smarty->assign('username', html_clean((isset($username)) ? $username : $user['username']));
	$smarty->assign('changepass_link', auto_gen_link('index.php?eps=profile&amp;action=changepass&amp;uid='.$uid, $eps_lang['Change_password']));
	$smarty->assign('email', html_clean((isset($email)) ? $email : $user['email']));
	$smarty->assign('s_id', html_clean((isset($s_id)) ? $s_id : $user['s_id']));
	$smarty->assign('groups', $groups);
	$smarty->assign('use_ajax', (isset($_POST['use_ajax']) || $user['ajax']) ? true : false);
	$smarty->assign('styles', get_stuff('style'));
	$smarty->assign('languages', get_stuff('language'));

	$smarty->assign('form_tag2', auto_gen_form('index.php?eps=profile&amp;uid='.$uid, 'another', true));
	$smarty->assign('native', html_clean((isset($native)) ? $native : $user['native']));
	$smarty->assign('address', html_clean((isset($address)) ? $address : $user['address']));
	$smarty->assign('phone', html_clean((isset($phone)) ? $phone : $user['phone']));
	$smarty->assign('yahoo', html_clean((isset($yahoo)) ? $yahoo : $user['yahoo']));

	$smarty->display('module/profile.tpl');
}

unset($errors, $need_old_pass, $action, $uid, $secr_key, $old_password, $new_password1, $new_password2, $req_fields, $user, $groups, $allow_group_id, $username, $email, $s_id);
?>
