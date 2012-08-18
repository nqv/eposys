<?php
/*
--------------------------------------------------------------------------------
     File:  class_mysql.php

    Class:  MYSQL DATABASE
   Author:  Quoc Viet [aFeLiOn] (based on PunBB)
    Begin:  2006-02-15

   Syntax:  new dblayer(DB_host, DB_user, DB_pass, DB_name, P_connect);
  Require:  Database Config

  Comment:
--------------------------------------------------------------------------------
*/

class dblayer
{
	var $link_id;
	var $query_result;
	var $saved_queries = array();

	function dblayer($db_host, $db_user, $db_pass, $db_name, $p_connect = false)
	{

		if ($p_connect)
			$this->link_id = @mysql_pconnect($db_host, $db_user, $db_pass);
		else
			$this->link_id = @mysql_connect($db_host, $db_user, $db_pass);

		if ($this->link_id)
		{
			if (@mysql_select_db($db_name, $this->link_id))
			{
				@mysql_query("SET NAMES 'UTF8'", $this->link_id);
				return $this->link_id;
			}
			else
				error('Unable To Select Database. MySQL Reported: '.mysql_error(), __FILE__, __LINE__);
		}
		else
			error('Unable To Connect To MySQL Server. MySQL Reported: '.mysql_error(), __FILE__, __LINE__);
	}

	function query($sql, $unbuffered = false)
	{
		$this->query_result = @mysql_query($sql, $this->link_id);
		if ($this->query_result)
			return $this->query_result;
		else
		{
			if (defined('EPS_DEBUG'))
				$this->saved_queries[] = array($sql, 0);
			return false;
		}
	}
	
	function result($query_id = 0, $row = 0)
	{
		return ($query_id) ? @mysql_result($query_id, $row) : false;
	}

	function fetch_assoc($query_id = 0)
	{
		return ($query_id) ? @mysql_fetch_assoc($query_id) : false;
	}

	function fetch_row($query_id = 0)
	{
		return ($query_id) ? @mysql_fetch_row($query_id) : false;
	}

	function num_rows($query_id = 0)
	{
		return ($query_id) ? @mysql_num_rows($query_id) : false;
	}

	function affected_rows()
	{
		return ($this->link_id) ? @mysql_affected_rows($this->link_id) : false;
	}

	function insert_id()
	{
		return ($this->link_id) ? @mysql_insert_id($this->link_id) : false;
	}

	function get_saved_queries()
	{
		return $this->saved_queries;
	}

	function free_result($query_id = false)
	{
		return ($query_id) ? @mysql_free_result($query_id) : false;
	}
	
	function escape($str)
	{
		if (function_exists('mysql_real_escape_string'))
			return mysql_real_escape_string($str, $this->link_id);
		else
			return mysql_escape_string($str);
	}
	
	function error()
	{
		$result['error_sql'] = @current(@end($this->saved_queries));
		$result['error_no'] = @mysql_errno($this->link_id);
		$result['error_msg'] = @mysql_error($this->link_id);
		return $result;
	}

	function close()
	{
		if ($this->link_id)
		{
			if ($this->query_result)
				@mysql_free_result($this->query_result);
			return @mysql_close($this->link_id);
		}
		else
			return false;
	}
}
?>
