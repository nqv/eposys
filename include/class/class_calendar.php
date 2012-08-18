<?php
/*
--------------------------------------------------------------------------------
     File:  class_calendar.php

    Class:  CALENDAR
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-01-26

   Syntax:  new calendar();
            ->create(Base_url);
  Require:
    + Object: $epsclass->xml;
    + Variable: $eps_user; $eps_lang;
    + Function: gen_ajax_link; eps_get_var;

  Comment:
--------------------------------------------------------------------------------
*/

class calendar
{
	var $diff; // Second
	var $timediff; // Now + $diff
	// Lang
	var $months;
	var $days;

	function calendar()
	{
		global $eps_user, $eps_lang;
		$this->months = array('', $eps_lang['January'], $eps_lang['February'], $eps_lang['March'], $eps_lang['April'], $eps_lang['May'], $eps_lang['June'], $eps_lang['July'], $eps_lang['August'], $eps_lang['September'], $eps_lang['October'], $eps_lang['November'], $eps_lang['December']);
		$this->days = array($eps_lang['Mo'], $eps_lang['Tu'], $eps_lang['We'], $eps_lang['Th'], $eps_lang['Fr'], $eps_lang['Sa'], $eps_lang['Su']);
		$this->diff = $eps_user['timezone'] * 3600;
		$this->timediff = time() + $this->diff;
		return true;
	}

	function get()
	{
		$m = eps_get_var('m', gmdate('n', $this->timediff));
		$y = eps_get_var('y', gmdate('Y', $this->timediff));
		if ($m < 1)
			$m = 1;
		if ($m > 12)
			$m = 12;
		if ($y < 1)
			$y = 1;

		return array($y, $m);
	}

	// My Style Weekday
	function my_wday($days)
	{
		return ($days['wday'] != 0) ? $days['wday'] - 1 : 6;
	}

	// getdate
	function get_date_info($m, $d, $y)
	{
		return getdate(gmmktime(0, 0, - $this->diff, $m, $d, $y));
	}

	// today_info
	function get_today_info()
	{
		return getdate(gmmktime() - $this->diff);
	}
	
	// gen date string
	function gen_date_str($infos)
	{
		return sprintf('%04d%02d%02d', $infos['year'], $infos['mon'], $infos['mday']);
	}

	// Get Event From XML
	function get_event()
	{
		/*
		global $epsclass;
		$epsclass->load_class('class_xml');
		$epsclass->xml->load_file(EPS_XML_DIR.'k48htd.xml');
		return $epsclass->xml->get_event();
		*/
		require EPS_DATA_DIR.'event.php';
		return $eps_events;
	}

	// Display
	function create($base_url)
	{
		if (strpos($base_url, '?') !== false)
			$base_url .= '&amp';
		else
			$base_url .= '?';

		$d_names = $this->days;
		$m_names = $this->months;
		list($this_y, $this_m) = $this->get();
		$events = $this->get_event();

		// Num days
		$this_m_numday = cal_days_in_month(CAL_GREGORIAN, $this_m, $this_y);

		$this_m_first_infos = $this->get_date_info($this_m, 1, $this_y);
		$this_m_last_infos = $this->get_date_info($this_m, $this_m_numday, $this_y);
		
		$today_infos = $this->get_today_info();
		$today_str = $this->gen_date_str($today_infos);

		// Navigator
		$nav_prev_m = $this_m - 1;
		$nav_prev_y = $nav_next_y = $this_y;
		$nav_next_m = $this_m + 1;
		
		if ($nav_prev_m < 1)
		{
			$nav_prev_m = 12;
			$nav_prev_y--;
		}
		
		if ($nav_next_m > 12)
		{
			$nav_next_m = 1;
			$nav_next_y++;
		}

		// Calendar's Start & End
		$start = 1 - $this->my_wday($this_m_first_infos);
		$end = $this_m_numday + (6 - $this->my_wday($this_m_last_infos));
		
		// Start Creating
		$cal = '<div class="calendar"><table cellspacing="0">'."\n";

		// Header
		$cal .= '<tr><td colspan="7" class="cal_head">';
		$cal .= '<span>'.gen_ajax_link('eps=calendar&amp;y='.$nav_prev_y.'&amp;m='.$nav_prev_m, '&laquo;', '', 'calendar_content').'</span>';
		$cal .= '<span>'.$m_names[$this_m].' - '.$this_y.'</span>';
		$cal .= '<span>'.gen_ajax_link('eps=calendar&amp;y='.$nav_next_y.'&amp;m='.$nav_next_m, '&raquo;', '', 'calendar_content').'</span>';
		$cal .= '</td></tr>'."\n";

		// Days of Week
		$cal .= '<tr class="cal_head2">';
		foreach ($d_names as $v)
			$cal .= '<td>'.$v.'</td>';
		// This Close Tag "</tr>" Is In Loop Below;

		// Days in Month
		for ($i = $start; $i <= $end; $i++)
		{
			$cur_infos = $this->get_date_info($this_m, $i, $this_y);
			$cur_str = $this->gen_date_str($cur_infos);

			// New Row
			if ($this->my_wday($cur_infos) == 0)
				$cal .= '</tr>'."\n".'<tr>';

			$title = $style = '';

			// Event
			if (!empty($events[$cur_str]))
			{
				$cur_infos['mday'] = '<strong>'.$cur_infos['mday'].'</strong>';
				$title = $events[$cur_str];
			}

			// Other Month
			if ($cur_infos['mon'] != $this_m)
				$style = 'cal_other';

			// Satuday, Sunday
			else if ($this->my_wday($cur_infos) == 5)
				$style = 'cal_sat';
			else if ($this->my_wday($cur_infos) == 6)
				$style = 'cal_sun';

			// Today
			if ($cur_str == $today_str)
				$style .= (empty($style)) ? 'cal_today' : ' cal_today';

			$cal .= '<td'.((!empty($style)) ?  ' class="'.$style.'"' : '').((!empty($title)) ?  ' title="'.$title.'"' : '').'>'.$cur_infos['mday'].'</td>';
		}
		
		$cal .= '</tr></table></div>';
		return $cal;
	}
}

?>
