<?php
/*
--------------------------------------------------------------------------------
     File:  class_antiflood.php

    Class:  ANTI FLOOD
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-01-23

   Syntax:  new antiflood();
            ->verify(Name, Type);
            ->update(Name, Type);
  Require:
   + Object:   [none]
   + Variable: $_SESSION;
   + Function: eps_session_start();

  Comment: Type = 1: Flood On Post, Search...
           Type = 2: Flood On Login...
--------------------------------------------------------------------------------
*/

class antiflood
{
	var $min_period = 30;
	var $free_try = 2;
	var $wait = 0;

	var $try_allow = true;
	var $period_allow = true;

	function antiflood($min_period = 30)
	{
		eps_session_start();
		$this->min_period = $min_period;
	}

	function verify($name, $type = 1)
	{
		// Period
		if ($type == 1)
		{
			$name = 'last_'.$name;

			if (!isset($_SESSION[$name]))
				$_SESSION[$name] = 0;

			$_SESSION[$name] = intval($_SESSION[$name]);
			$now = time();
			if (($now - $_SESSION[$name]) < $this->min_period)
			{
				$this->period_allow = false;
				$this->wait = ($this->min_period - ($now - $_SESSION[$name]));
				return false;
			}
			else
			{
				$this->period_allow = true;
				return true;
			}
		}
		// Try
		else if ($type == 2)
		{
			$name = 'try_'.$name;

			if (!isset($_SESSION[$name]))
				$_SESSION[$name] = 0;

			$_SESSION[$name] = intval($_SESSION[$name]);
			if ($_SESSION[$name] == $this->free_try - 1)
			{
				$this->try_allow = false;
				$_SESSION[$name]++;
				return true;
			}
			else if ($_SESSION[$name] > $this->free_try - 1)
			{
				$this->try_allow = false;
				$_SESSION[$name]++;
				return false;
			}
			else
			{
				$this->try_allow = true;
				$_SESSION[$name]++;
				return true;
			}
		}
		else
			exit('Unknown Flood Type');
	}

	function update($name, $type)
	{
		if ($type == 1)
			$_SESSION['last_'.$name] = time();	
		else if ($type == 2)
			$_SESSION['try_'.$name] = 0;
		else
			exit('Unknown Flood Type');
	}
}

?>
