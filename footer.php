<?php
/*
--------------------------------------------------------------------------------
     File:  footer.php

     Unit:  FOOTER
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-01-02

  Comment:  Grap OB-Contents & Show off

--------------------------------------------------------------------------------
*/
if (!defined('IN_EPS'))
	exit;

$tpl_eps_main = trim(ob_get_contents());

// Require Field Name
// if (!empty($req_fields))
// 	$tpl_eps_main = gen_jslang($req_fields).$tpl_eps_main;

$smarty->assign('tpl_eps_main', $tpl_eps_main);
ob_end_clean();

// $js_vn_typing = (isset($vn_typing) && $vn_typing) ? '<script type="text/javascript" src="js/him.js"></script>' : '';
// $tpl_eps_addon = '<script type="text/javascript" src="js/avim.js"></script>';
// $smarty->assign('tpl_eps_addon', $tpl_eps_addon);

$smarty->display("main.tpl");

$epsclass->db->close();
exit;
