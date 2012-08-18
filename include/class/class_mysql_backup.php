<?php
/*
--------------------------------------------------------------------------------
     File:  class_mysql_backup.php

    Class:  MYSQL DATABASE BACKUP
   Author:  Quoc Viet [aFeLiOn] (based on Vagharshak Tozalakyan)
    Begin:  2006-02-15

   Syntax:  new mysql_backup(Drop_tables, Struct_only, Backup_dir);
            ->backup(Task, Tables, Compress);
  Require:  Mysql connected

  Comment:  Task = 1 -> Save
            Task = 2 -> Download
--------------------------------------------------------------------------------
*/

class mysql_backup
{
	var $server;
	var $database;
	var $tables = array();
	var $drop_tables;
	var $struct_only;
	var $backup_dir;
	var $error = '';

	function mysql_backup($drop_tables = true, $struct_only = false, $backup_dir = './backup/')
	{
		global $eps_db_config, $eps_config;
		if (!is_dir($backup_dir))
			mkdir($backup_dir, 0777);
		else if (!is_writeable($backup_dir))
			chmod($backup_dir, 0777);

		$this->backup_dir = $backup_dir;
		$this->drop_tables = $drop_tables;
		$this->struct_only = $struct_only;
		$this->server = $eps_config['base_url'];
		$this->database = $eps_db_config['name'];
	}

	function backup($task = '', $tables = array(), $compress = true)
	{
		if (!is_array($tables))
		{
			if (!empty($tables))
				$this->tables[] = $tables;
		}
		else
			$this->tables = $tables;

		if (!($sql = $this->_retrieve()))
		{
			return false;
		}
		
		// 1: Save; 2: Download
		if ($task == 1 || $task == 2)
		{
			$fname = $this->backup_dir.$this->database.'_'.date('ymd');
			$fname .= ($compress) ? '.sql.gz' : '.sql';
			return ($task == 1) ? $this->_save_file($fname, $sql, $compress) : $this->_download_file($fname, $sql, $compress);
		}
		else
		{
			return $sql;
		}
	}


	function _get_tables()
	{
  		global $epsclass;
		$values = array();

		$result = $epsclass->db->query('SHOW TABLES') or error('Unable to Show tables', __FILE__, __LINE__, $epsclass->db->error());

		while ($row = $epsclass->db->fetch_row($result))
		{
			if (empty($this->tables) || in_array($row[0], $this->tables))
				$values[] = $row[0];
		}

		if (!count($values))
		{
			$this->error = 'No tables found in database.';
			return false;
		}
		return $values;
	}


	function _dump_table($table)
	{
		global $epsclass;
		$value = '';
		$epsclass->db->query('LOCK TABLES `'.$table.'` WRITE') or error('Unable to Lock tables', __FILE__, __LINE__, $epsclass->db->error());

		if ($this->drop_tables)
			$value .= 'DROP TABLE IF EXISTS `'.$table. '`;'."\n";

		$result = $epsclass->db->query('SHOW CREATE TABLE `'.$table.'`') or error('Unable to Show create table', __FILE__, __LINE__, $epsclass->db->error());

		$row = $epsclass->db->fetch_assoc($result);
		$value .= $row['Create Table'].';'."\n\n";

		if (!$this->struct_only)
			$value .= $this->_get_inserts($table);

		$value .= "\n\n";
		$epsclass->db->query('UNLOCK TABLES') or error('Unable to Unlock tables', __FILE__, __LINE__, $epsclass->db->error());
		return $value;
	}


	function _get_inserts($table)
	{
		global $epsclass;
		$value = '';

		$result = $epsclass->db->query('SELECT * FROM `'.$table.'`') or error('Unable to Select in table', __FILE__, __LINE__, $epsclass->db->error());
		while ($row = $epsclass->db->fetch_row($result))
		{
			$values = array();
			foreach ($row as $data)
			{
				if (is_null($data))
					$values[] = 'NULL';
				else
					$values[] .= '\''.addslashes($data).'\'';
			}
			$value .= 'INSERT INTO `'.$table.'` VALUES ('.implode(', ', $values).');'."\n";
		}
		return $value;
	}


	function _retrieve()
	{
		$value = '#'."\n";
		$value .= '# Eposys MySQL database dump'."\n";
		$value .= '# Host: '.$this->server."\n";
		$value .= '# Generated: '.date('M j, Y').' at '.date('H:i')."\n";
		$value .= '# MySQL version: '.mysql_get_server_info()."\n";
		$value .= '# PHP version: '.phpversion()."\n";
		$value .= '# Database: `'.$this->database.'`'."\n";
		$value .= '#'."\n\n";
		if (!($tables = $this->_get_tables()))
		{
			return false;
		}

		foreach ($tables as $table)
		{
			if (!($table_dump = $this->_dump_table($table)))
			{
				return false;
			}
			$value .= $table_dump;
		}

		return $value;
	}


	function _save_file($fname, $sql, $compress)
	{
		if ($compress)
		{
			if (!($zf = gzopen($fname, 'w9')))
			{
				$this->error = 'Can\'t create the output file.';
				return false;
			}
			gzwrite($zf, $sql);
			gzclose($zf);
		}
		else
		{
			if (!($f = fopen($fname, 'w')))
			{
				$this->error = 'Can\'t create the output file.';
				return false;
			}
			fwrite($f, $sql);
			fclose($f);
		}
		return true;
	}


	function _download_file($fname, $sql, $compress)
	{
		ob_end_clean();
		header('Content-type: application/force-download');
		header('Content-Disposition: attachment; filename="'.basename($fname).'"');
		header('Content-Transfer-Encoding: binary');
		header('Pragma: no-cache');
		header('Expires: 0');
		header('Connection: close');
		echo ($compress) ? gzencode($sql) : $sql;
		exit;
	}
}

?>
