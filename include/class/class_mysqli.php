<?php
/*
--------------------------------------------------------------------------------
     File:  class_mysql.php

    Class:  MYSQLi DATABASE
   Author:  Quoc Viet [aFeLiOn] (based on PunBB)
    Begin:  2005-12-30

   Syntax:  new dblayer(DB_host, DB_username, DB_password, DB_name);
  Require:  Database Config

  Comment:
--------------------------------------------------------------------------------
*/

class dblayer
{
	var $link_id;
	var $query_result;
	var $saved_queries = array();

	function dblayer($db_host, $db_user, $db_pass, $db_name, $foo = false)
	{
		// Support MySQLi
		if (!function_exists('mysqli_connect'))
			exit('This PHP environment doesn\'t have Improved MySQL (mysqli) support built in.');

		if (strpos($db_host, ':') !== false)
			list($db_host, $db_port) = explode(':', $db_host);

		if (isset($db_port))
			$this->link_id = @mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
		else
			$this->link_id = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
		
		if ($this->link_id)
			@mysqli_query($this->link_id, "SET NAMES 'UTF8'");
		else
			error('Unable to connect to MySQL and select database. MySQL reported: '.mysqli_connect_error(), __FILE__, __LINE__);
	}

	function query($sql, $unbuffered = false)
	{
		$this->query_result = @mysqli_query($this->link_id, $sql);

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
		if ($query_id)
		{
			if ($row)
				@mysqli_data_seek($query_id, $row);

			$cur_row = @mysqli_fetch_row($query_id);
			return $cur_row[0];
		}
		else
			return false;
	}

	function fetch_assoc($query_id = 0)
	{
		return ($query_id) ? @mysqli_fetch_assoc($query_id) : false;
	}

	function fetch_row($query_id = 0)
	{
		return ($query_id) ? @mysqli_fetch_row($query_id) : false;
	}

	function num_rows($query_id = 0)
	{
		return ($query_id) ? @mysqli_num_rows($query_id) : false;
	}

	function affected_rows()
	{
		return ($this->link_id) ? @mysqli_affected_rows($this->link_id) : false;
	}

	function insert_id()
	{
		return ($this->link_id) ? @mysqli_insert_id($this->link_id) : false;
	}

	function get_saved_queries()
	{
		return $this->saved_queries;
	}

	function free_result($query_id = false)
	{
		return ($query_id) ? @mysqli_free_result($query_id) : false;
	}

	function escape($str)
	{
		return mysqli_real_escape_string($this->link_id, $str);
	}

	function error()
	{
		$result['error_sql'] = @current(@end($this->saved_queries));
		$result['error_no'] = @mysqli_errno($this->link_id);
		$result['error_msg'] = @mysqli_error($this->link_id);
		return $result;
	}

	function close()
	{
		if ($this->link_id)
		{
			if ($this->query_result)
				@mysqli_free_result($this->query_result);
			return @mysqli_close($this->link_id);
		}
		else
			return false;
	}
}
