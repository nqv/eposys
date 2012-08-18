<?php
/*
--------------------------------------------------------------------------------
     File:  header.php

     Unit:  HEADER
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-01-02

  Comment:  Header send

--------------------------------------------------------------------------------
*/
if (!defined('IN_EPS'))
	exit;

// No-cache
header('Expires: Wed, 21 Mar 1984 01:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Template: Title, Style
$smarty->assign('tpl_eps_title', $eps_title);

// {$tpl_eps_main}
ob_start();

define('HEADER_LOADED', true);
