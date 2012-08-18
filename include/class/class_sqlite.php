<?php
/*
--------------------------------------------------------------------------------
     File:  class_sqlite.php

    Class:  SQLITE DATABASE
   Author:  Quoc Viet [aFeLiOn] (modify form PunBB)
    Begin:  2006-03-11

   Syntax:  new dblayer(DB_host, DB_username, DB_password, DB_name, P_connect);
  Require:  Database Config

  Comment:
--------------------------------------------------------------------------------
*/

class dblayer
{
	var $link_id;
	var $query_result;

	var $saved_queries = array();

	var $error_no = false;
	var $error_msg = 'Unknown';

	function dblayer($db_host, $db_user, $db_pass, $db_name, $p_connect = false)
	{
		// Support SQLite
		if (!function_exists('sqlite_open'))
			exit('This PHP environment doesn\'t have SQLite support built in. SQLite support is required if you want to use a SQLite database to run this forum. Consult the PHP documentation for further assistance.');

		// Prepend $db_name with the path to the forum root directory
		$db_name = EPS_ROOT.$db_name;

		if (!file_exists($db_name))
		{
			@touch($db_name);
			@chmod($db_name, 0666);
			if (!file_exists($db_name))
				error('Unable to create new database \''.$db_name.'\'. Permission denied', __FILE__, __LINE__);
		}

		if (!is_readable($db_name))
			error('Unable to open database \''.$db_name.'\' for reading. Permission denied', __FILE__, __LINE__);

		if (!is_writable($db_name))
			error('Unable to open database \''.$db_name.'\' for writing. Permission denied', __FILE__, __LINE__);

		if ($p_connect)
			$this->link_id = @sqlite_popen($db_name, 0666, $sqlite_error);
		else
			$this->link_id = @sqlite_open($db_name, 0666, $sqlite_error);

		if (!$this->link_id)
			error('Unable to open database \''.$db_name.'\'. SQLite reported: '.$sqlite_error, __FILE__, __LINE__);
		else
		{
			return $this->link_id;
		}
	}

	function query($sql, $unbuffered = false)
	{
		if ($unbuffered)
			$this->query_result = @sqlite_unbuffered_query($this->link_id, $sql);
		else
			$this->query_result = @sqlite_query($this->link_id, $sql);

		if ($this->query_result)
		{
			if (defined('EPS_DEBUG'))
				$this->saved_queries[] = array($sql, sprintf('%.5f', get_microtime() - $q_start));

			return $this->query_result;
		}
		else
		{
			if (defined('EPS_DEBUG'))
				$this->saved_queries[] = array($sql, 0);

			$this->error_no = @sqlite_last_error($this->link_id);
			$this->error_msg = @sqlite_error_string($this->error_no);

			return false;
		}
	}

	function result($query_id = 0, $row = 0)
	{
		if ($query_id)
		{
			if ($row != 0)
				@sqlite_seek($query_id, $row);

			return @current(@sqlite_current($query_id));
		}
		else
			return false;
	}

	function fetch_assoc($query_id = 0)
	{
		if ($query_id)
		{
			$cur_row = @sqlite_fetch_array($query_id, SQLITE_ASSOC);
			if ($cur_row)
			{
				// Horrible hack to get rid of table names and table aliases from the array keys
				while (list($key, $value) = @each($cur_row))
				{
				    $dot_spot = strpos($key, '.');
				    if ($dot_spot !== false)
				    {
				        unset($cur_row[$key]);
				        $key = substr($key, $dot_spot+1);
				        $cur_row[$key] = $value;
				    }
				}
			}

			return $cur_row;
		}
		else
			return false;
	}

	function fetch_row($query_id = 0)
	{
		return ($query_id) ? @sqlite_fetch_array($query_id, SQLITE_NUM) : false;
	}

	function num_rows($query_id = 0)
	{
		return ($query_id) ? @sqlite_num_rows($query_id) : false;
	}

	function affected_rows()
	{
		return ($this->query_result) ? @sqlite_changes($this->query_result) : false;
	}

	function insert_id()
	{
		return ($this->link_id) ? @sqlite_last_insert_rowid($this->link_id) : false;
	}

	function get_saved_queries()
	{
		return $this->saved_queries;
	}

	function free_result($query_id = false)
	{
		return true;
	}

	function escape($str)
	{
		return sqlite_escape_string($str);
	}

	function error()
	{
		$result['error_sql'] = @current(@end($this->saved_queries));
		$result['error_no'] = $this->error_no;
		$result['error_msg'] = $this->error_msg;

		return $result;
	}

	function close()
	{
		if ($this->link_id)
		{
			if ($this->in_transaction)
			{
				if (defined('EPS_DEBUG'))
					$this->saved_queries[] = array('COMMIT', 0);

				@sqlite_query($this->link_id, 'COMMIT');
			}

			return @sqlite_close($this->link_id);
		}
		else
			return false;
	}
}
