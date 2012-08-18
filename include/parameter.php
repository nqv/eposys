<?php
/*
--------------------------------------------------------------------------------
     File:  parameter.php

     Unit:  Set up parameter
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-03-04

  Comment:
--------------------------------------------------------------------------------
*/

if (!defined('IN_EPS'))
	exit;

// Constant (DIRECTORY MUST END WITH A SLASH !!!)
define('EPS_CLASS_DIR', './include/class/');
define('EPS_CACHE_DIR', './cache/');
define('EPS_LIBRARY_DIR', './include/library/');
define('EPS_ALBUM_DIR', './upload/album/c7805285ebf4da1249dd22ce219ae03e/k48htd/');
define('EPS_GALLERY_DIR', './upload/album/c7805285ebf4da1249dd22ce219ae03e/user/');
define('EPS_SHARE_DIR', './upload/share/3078c3c38774f773121b18148dba4583/');
define('EPS_DATA_DIR', './data/');

define('EPS_ADMIN', 1);
define('EPS_MOD', 2);
define('EPS_MEMBER', 4);
define('EPS_GUEST', 5);

define('TBL_CONFIG', 'e_config');
define('TBL_USER', 'e_users');
define('TBL_GROUP', 'e_groups');
define('TBL_NEWS', 'e_news');
define('TBL_SHARE', 'e_share');
define('TBL_K48HTD', 'e_k48htd');
define('TBL_K48MARK', 'e_k48mark');
define('TBL_GALLERY', 'e_gallery');
define('TBL_GALLERY_CM', 'e_gallery_cm');

define('SMARTY_DIR', './include/smarty/');
//define('SMARTY_DIR', 'C:/server/htdocs/eposys/include/smarty/');

define('FILE_CACHE_CONFIG', EPS_CACHE_DIR.'cache_config.php');
define('FILE_POLL_DATA', EPS_DATA_DIR.'poll_data');
define('FILE_POLL_ID', EPS_DATA_DIR.'polled_id');
define('FILE_POLL_IP', EPS_DATA_DIR.'polled_ip');
define('FILE_K48_XML', EPS_DATA_DIR.'k48htd.xml');

$eps_course = array('k48htd1', 'k48htd2', 'k48htd3', 'k48htd4', 'k48htdp');

?>
