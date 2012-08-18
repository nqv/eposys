<?php
/*
--------------------------------------------------------------------------------
     File:  visual_confirm.php

   Module:  VISUAL CONFIRM
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-01-20

  Comment:  Return A Image
--------------------------------------------------------------------------------
*/

if (!defined('IN_EPS'))
	exit();

require EPS_ROOT.'include/function_visual.php';

$code = eps_get_var('code', '');
$pos = eps_get_var('pos', 0);

$encrypt = eps_encrypt($code, 6);
if ($code == '' || $pos == 0 || $pos > strlen($encrypt))
	exit;

header('Content-Type: image/png');
header('Cache-control: no-cache, no-store');
echo chr2img(substr($encrypt, $pos - 1, 1));
exit;

?>
