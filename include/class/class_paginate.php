<?php
/*
--------------------------------------------------------------------------------
     File:  class_paginate.php

    Class:  PAGINATE
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-01-06

   Syntax:  new paginate();
            ->get_result(Sqls[array], Base_url, Current_page, Per_page = 20); [SQL Result]
            ->num_result();
            ->gen_page_link(); [Generate Link]
  Require:
    + Object:   $epsclass->db;
	 + Variable: $eps_config; $eps_lang;
    + Function: auto_gen_link();

  Comment:
--------------------------------------------------------------------------------
*/

class paginate
{
	var $sql = '';
	var $base_url = '';
	var $cur_page = 0;
	var $num_item = 0;
	var $per_page = 20;

	// Number Of Items
	function get_num_item($sql, $real_count = false)
	{
		global $epsclass;
		$result = $epsclass->db->query($sql) or error('Unable to paginage', __FILE__, __LINE__, $epsclass->db->error());
		if ($real_count)
			return $epsclass->db->result($result);
		else
			return $epsclass->db->num_rows($result);
	}

	// Return Paginated Result
	function get_result($sqls, $base_url, $cur_page, $per_page = 20)
	{
		global $epsclass;

		if (is_array($sqls))
			list($sql_count, $this->sql) = $sqls;
		else
		{
			$sql_count = '';
			$this->sql = $sqls;
		}
		$this->base_url = $base_url;
		$this->cur_page = intval($cur_page);
		$this->per_page = intval($per_page);

		// Num Items
		$this->num_item = $this->get_num_item($sql_count, ($sql_count != '') ? true : false);

		// Do
		$start = $this->per_page * ($this->cur_page - 1);
		if ($start >= $this->num_item)
			$start = 0;
		$result = $epsclass->db->query($this->sql.' LIMIT '.$start.','.$this->per_page) or error('Unable to paginate', __FILE__, __LINE__, $epsclass->db->error());

		return $result;
	}
	
	// Page Link
	function gen_page_link()
	{
		global $eps_config, $eps_lang;
		$pages = array();

		$url = $this->base_url;
		$cur = $this->cur_page;

		$total_page = ceil($this->num_item / $this->per_page);

		if ($total_page <= 1)
			$pages = array('<span><strong>1</strong></span>');
		else
		{
			if (strpos($url, '?'))
				$url .= '&amp;';
			else
				$url .= '?';

			// Previous
			if ($cur > 1)
				$pages[] = auto_gen_link($url.'p='.($cur - 1), '&lt', $eps_lang['Previous_page'], true);

			// 1 2...
			if ($cur >= 4)
			{
				$pages[] = auto_gen_link($url.'p=1', '1', '1', true);
				if ($cur >= 5)
					$pages[] = auto_gen_link($url.'p=2', '2', '2', true);
				if ($cur >= 6)
					$pages[] = '&hellip;';
			}

			// 4 5 [6] 7 8
			for ($i = $cur - 2, $stop = $cur + 2; $i <= $stop; $i++)
			{
				if ($i < 1 || $i > $total_page)
					continue;
				else if ($i != $cur)
					$pages[] = auto_gen_link($url.'p='.$i, $i, $i, true);
				else
					$pages[] = '<span><strong>'.$i.'</strong></span>';
			}

			// ...10 11
			if ($cur <= ($total_page - 3))
			{
				if ($cur <= ($total_page - 5))
					$pages[] = '&hellip;';
				if ($cur <= ($total_page - 4))
					$pages[] = auto_gen_link($url.'p='.($total_page - 1), ($total_page - 1), ($total_page - 1), true);
				$pages[] = auto_gen_link($url.'p='.$total_page, $total_page, $total_page, true);
			}

			// Next
			if ($cur < $total_page)
				$pages[] = auto_gen_link($url.'p='.($cur + 1), '&gt', $eps_lang['Next_page'], true);
		}
		return '<span>'.$eps_lang['Page'].'</span> '.implode(' ', $pages);
	}
	
	function num_result()
	{
		return $this->num_item;
	}
}

?>
