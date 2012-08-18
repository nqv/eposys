<?php
/*
--------------------------------------------------------------------------------
     File:  extend_db.php

   Extend:  Database
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-03-11

   Syntax:  new extend_db($template_dir);

  Require:
   + Class: dblayer

  Comment:
--------------------------------------------------------------------------------
*/
class extend_db extends dblayer
{
	function extend_db($db_host, $db_user, $db_pass, $db_name, $p_connect = false)
	{
		$this->dblayer($db_host, $db_user, $db_pass, $db_name, $p_connect);
	}

	// When $where_field == true, becareful with SQL Injection !!!
	function vselect($table, $fields, $where_value = '', $where_field = 'id')
	{
		if ($fields === true || $fields == '*')
			$field_str = '*';
		else if (is_array($fields))
			$field_str = '`'.implode('`,`', $fields).'`';
		else
			$field_str = $fields;
	
		$where_sql = ($where_value != '') ? ' '.(($where_field === true) ? " $where_value" : " WHERE `$where_field`='".$this->escape($where_value)."'") : '';
	
		$result = $this->query("SELECT $field_str FROM `$table`$where_sql") or error('Unable to select from database', __FILE__, __LINE__, $this->error());
	
		return $result;
	}

	// $field_values must is array
	function vinsert($table, $field_values)
	{
		$fields = $values = array();
		foreach ($field_values as $k => $v)
		{
			$fields[] = $k;
			$values[] = $this->escape($v);
		}
		$this->query("INSERT INTO `$table`(`".implode("`,`", $fields)."`) VALUES('".implode("','", $values)."')") or error('Unable to insert to database', __FILE__, __LINE__, $this->error());
	}

	function vupdate($table, $field_values, $where_value, $where_field = 'id')
	{
		$updates = array();
	
		foreach ($field_values as $k => $v)
			$updates[] = "`$k`='".$this->escape($v)."'";
	
		$where_sql = ($where_field === true) ? " $where_value" : " WHERE `$where_field`='".$this->escape($where_value)."'";
	
		$this->query("UPDATE `$table` SET ".implode(',', $updates).$where_sql) or error('Unable to update database', __FILE__, __LINE__, $this->error());
	}

	function vdelete($table, $where_value, $where_field = 'id')
	{
		$where_sql = ($where_field === true) ? " $where_value" : " WHERE `$where_field`='".$this->escape($where_value)."'";
		$this->query("DELETE FROM `$table`$where_sql") or error('Unable to delete in database', __FILE__, __LINE__, $this->error());
	}
}
?>
