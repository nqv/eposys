<?php
/*
--------------------------------------------------------------------------------
     File:  calendar.php

   Module:  CALENDAR
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-01-27

  Comment:
--------------------------------------------------------------------------------
*/

if (!defined('IN_EPS'))
	exit;

$epsclass->load_class('class_calendar');
echo $epsclass->calendar->create('index.php');
?>
