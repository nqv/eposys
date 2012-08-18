<?php
/*
--------------------------------------------------------------------------------
     File:  class_validate.php

    Class:  VALIDATE
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-01-09

   Syntax:  new validate();
            ->chk_length(String, Min_length, Max_length = null, Name);
            ->chk_username(Username, Registered_id = null);
            ->chk_email(Email, Registered_id = null);
            ->chk_s_id(Student_id, Registered_id = null, Send = false);
            ->chk_match(String_A, String_B , Name);
            ->chk_empty(String, Name);
  Require:
    + Object:   $epsclass->db;
	 + Variable: $eps_lang;
    + Function: eps_strlen(); eps_strtolower(); eps_random();

  Comment:  Called In function_validate.php
--------------------------------------------------------------------------------
*/

class validate
{
	var $errors = array();
	var $secr_len = 20;

	// Reset
	function data_reset()
	{
		$this->errors = array();
	}

	// Min-Max Length
	function chk_length($str, $min, $max, $name)
	{
		global $eps_lang;
		if ($str == '')
		{
			$this->errors[] = $name.': '.$eps_lang['Validate_empty'];
			return false;
		}
		else if (eps_strlen($str) < $min)
		{
			$this->errors[] = $name.': '.$eps_lang['Validate_too_short'];
			return false;
		}
		else if (($max > $min) && (eps_strlen($str) > $max))
		{
			$this->errors[] = $name.': '.$eps_lang['Validate_too_long'];
			return false;
		}
		else
			return true;
	}

	// Validate Username ($registered_id Is For Profile Changing)
	function chk_username($username, $registered_id = null)
	{
		global $epsclass, $eps_lang;

		if (!$this->chk_length($username, 2, 25, $eps_lang['Username']))
			return false;

		if (!preg_match('#[\[\]\"\'\?\(\)\<\>\{\};]#ui', $username))
		{
			if (!preg_match('#(^[0-9]+$)|(^[^a-z0-9]+$)#ui', $username))
			{
				$username = eps_strtolower($username);
				if ($username == 'guest' || $username == eps_strtolower($eps_lang['Guest']) || $username == 'admin' || $username == eps_strtolower($eps_lang['Admin']))
				{
					$this->errors[] = $eps_lang['Validate_username'];
					return false;
				}
				else
				{
					$sql = "SELECT 1 FROM ".TBL_USER." WHERE (LOWER(username)='".$epsclass->db->escape($username)."' OR LOWER(username)='".$epsclass->db->escape(preg_replace('#[^\w]#u', '', $username))."')";
					if ($registered_id > 0)
						$sql .= ' AND id!='.$registered_id;

					$result = $epsclass->db->query($sql) or error('Unable to fetch user info', __FILE__, __LINE__, $epsclass->db->error());
					if ($epsclass->db->num_rows($result))
					{
						$this->errors[] = $eps_lang['Username'].': '.$eps_lang['Validate_duplicate'];
						$epsclass->db->free_result($result);
						return false;
					}
					else
					{
						$epsclass->db->free_result($result);
						return true;
					}
				}
			}
			else
			{
				$this->errors[] = $eps_lang['Username'].': '.$eps_lang['Validate_invalid'];
				return false;			
			}
		}
		else
		{
			$this->errors[] = $eps_lang['Username'].': '.$eps_lang['Validate_invalid_char'];
			return false;
		}
	}

	// Validate Email ($registered_id Is For Profile Changing, $send For Send Mail)
	function chk_email($email, $registered_id = null, $send = false)
	{
		global $epsclass, $eps_lang;

		if (preg_match('#^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$#is', $email))
		{
			if (!$send)
			{
				$sql = "SELECT 1 FROM ".TBL_USER." WHERE email='".$epsclass->db->escape($email)."'";
				if ($registered_id > 0)
					$sql .= ' AND id!='.$registered_id;

				$result = $epsclass->db->query($sql) or error('Unable to fetch user info', __FILE__, __LINE__, $epsclass->db->error());
				if ($epsclass->db->num_rows($result))
				{
					$this->errors[] = $eps_lang['Email'].': '.$eps_lang['Validate_duplicate'];
					$epsclass->db->free_result($result);
					return false;
				}
				else
				{
					$epsclass->db->free_result($result);
					return true;
				}
			}			
		}
		else
		{
			$this->errors[] = $eps_lang['Email'].': '.$eps_lang['Validate_invalid'];
			return false;
		}
	}

	// Validate Student ID ($registered_id Is For Profile Changing)
	function chk_s_id($s_id, $registered_id = null)
	{
		global $epsclass, $eps_lang;

		if (preg_match('#^200[23][c\d]{4}$#uis', $s_id))
		{
			$sql = "SELECT s_id FROM ".TBL_USER." WHERE s_id='".$epsclass->db->escape($s_id)."'";
			if ($registered_id > 0)
				$sql .= ' AND id!='.$registered_id;

			$result = $epsclass->db->query($sql) or error('Unable to fetch user info', __FILE__, __LINE__, $epsclass->db->error());
			if ($epsclass->db->num_rows($result))
			{
				$this->errors[] = $eps_lang['StudentID'].': '.$eps_lang['Validate_duplicate'];
				$epsclass->db->free_result($result);
				return false;
			}
			else
			{
				$epsclass->db->free_result($result);
				return true;
			}
		}
		else
		{
			$this->errors[] = $eps_lang['StudentID'].': '.$eps_lang['Validate_invalid'];
			return false;
		}
	}
	
	// Confirm
	function chk_match($a, $b , $name)
	{
		global $eps_lang;

		if ($a != $b)
		{
			$this->errors[] = $name.': '.$eps_lang['Validate_not_match'];
			return false;
		}
		else
			return true;
	}

	// Check Empty
	function chk_empty($a, $name)
	{
		global $eps_lang;

		if (empty($a))
		{
			$this->errors[] = $name.': '.$eps_lang['Validate_empty'];
			return false;
		}
		else
			return true;
	}
	
	// Security String Create
	function secr_create()
	{
		return eps_random($this->secr_len).floor(time() / (30 * 24 * 3600));
	}
	
	// Security String Verify
	function secr_verify($code)
	{
		if (empty($code))
			return false;
		$secr_expire = substr($code, $this->secr_len, strlen($code));
		if (floor(time() / (30 * 24 * 3600)) - $secr_expire > 6)
		{
			$this->errors[] = $eps_lang['Code'].': '.$eps_lang['Validate_invalid'];
			return false;
		}
		else
			return true;
	}

}
?>
