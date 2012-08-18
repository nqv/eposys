<?php
/*
--------------------------------------------------------------------------------
     File:  test.php

   Module:  TESTING
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-06-08

  Comment:
--------------------------------------------------------------------------------
*/

define('EPS_ROOT', './');
define('IN_EPS', true);
require EPS_ROOT.'include/parameter.php';

?>
<table width="296" border="1">
  <tr>
    <td colspan="2"><div align="center">PHP v<? echo phpversion() ?> | MySQL v<? echo mysql_get_server_info() ?> </div></td>
  </tr>
  <tr>
    <td colspan="2"><strong>Directory's reading &amp; writing</strong></td>
  </tr>
  <tr>
    <td width="80%"><div align="right">Cache dir</div></td>
    <td><div align="center"><? echo (!is_readable(EPS_CACHE_DIR) || !is_writable(EPS_CACHE_DIR)) ? '<font color="red">FAILURE!</font>' : '<font color="green">Ok</font>' ?></div></td>
  </tr>
  <tr>
    <td><div align="right">Data dir</div></td>
    <td><div align="center"><? echo (!is_readable(EPS_DATA_DIR) || !is_writable(EPS_DATA_DIR)) ? '<font color="red">FAILURE!</font>' : '<font color="green">Ok</font>' ?></div></td>
  </tr>
  <tr>
    <td><div align="right">Smarty Compile dir</div></td>
    <td><div align="center"><? echo (!is_readable(SMARTY_DIR.'templates_c/') || !is_writable(SMARTY_DIR.'templates_c/')) ? '<font color="red">FAILURE!</font>' : '<font color="green">Ok</font>' ?></div></td>
  </tr> 
  <tr>
    <td colspan="2"><strong>Database</strong></td>
  </tr>
  <tr>
    <td><div align="right">Mysqli</div></td>
    <td><div align="center"><? echo (!extension_loaded('mysqli')) ? '<font color="gold">No!</font>' : '<font color="green">Yes</font>' ?></div></td>
  </tr>
  <tr>
    <td colspan="2"><strong>Extension</strong></td>
  </tr>
  <tr>
    <td><div align="right">Multibyte string</div></td>
    <td><div align="center"><? echo (!function_exists('mb_substr')) ? '<font color="gold">No!</font>' : '<font color="green">Yes</font>' ?></div></td>
  </tr>
  <tr>
    <td><div align="right">GD</div></td>
    <td><div align="center"><? echo (!extension_loaded('gd')) ? '<font color="gold">No!</font>' : '<font color="green">Yes</font>' ?></div></td>
  </tr>
  <tr>
    <td><div align="right">SimpleXML</div></td>
    <td><div align="center"><? echo (!function_exists('simplexml_load_file') || !function_exists('simplexml_load_file')) ? '<font color="gold">No!</font>' : '<font color="green">Yes</font>' ?></div></td>
  </tr>
  <tr>
    <td colspan="2"><strong>Other</strong></td>
  </tr>
  <tr>
    <td><div align="right">Allow Url File Open</div></td>
    <td><div align="center"><? echo (ini_get('allow_url_fopen') == '0' || strtolower(ini_get('allow_url_fopen')) == 'off') ? '<font color="red">OFF!</font>' : '<font color="green">On</font>' ?></div></td>
  </tr>
  <tr>
    <td><div align="right">In Safe-mode</div></td>
    <td><div align="center"><? echo (ini_get('safe_mode') == '0' || strtolower(ini_get('safe_mode')) == 'off') ? '<font color="red">OFF!</font>' : '<font color="green">On</font>' ?></div></td>
  </tr>
  <tr>
    <td><div align="right">Max File Upload</div></td>
    <td><div align="center"><? echo (ini_get('file_uploads') == '0' || strtolower(ini_get('file_uploads')) == 'off') ? '<font color="red">OFF!</font>' : '<font color="green">'.ini_get('upload_max_filesize').'</font>' ?></div></td>
  </tr>
</table>
<hr />
<?
$diff = floor((gmmktime(0,0,0,1,1,2006) - mktime(0,0,0,1,1,2006)) / 3600);
$TimeZone = $diff * 3600;
$infos1 = getdate(mktime(0, 0, 0, 1, 1, 2006));
$infos2 = getdate(gmmktime(0, 0, 0, 1, 1, 2006));
$infos3 = getdate(gmmktime(0, 0, - $TimeZone, 1, 1, 2006));
$today1 = getdate(mktime());
$today2 = getdate(gmmktime());
$today3 = getdate(gmmktime() - $TimeZone);

?>

<pre>
Time Difference: <?php echo $diff ?><br />
date: <? echo date("Y-m-d H:i:s", time()) ?><br />
gmdate: <? echo gmdate("Y-m-d H:i:s", time()) ?><br />
date <?php echo $diff ?>: <? echo date("Y-m-d H:i:s", time() + $TimeZone) ?><br />
gmdate <?php echo $diff ?>: <? echo gmdate("Y-m-d H:i:s", time() + $TimeZone) ?><br /><br />
<hr />
getdate-mktime: (1/1/2006 00:00:00):<? print_r($infos1) ?><br />
getdate-gmmktime: (1/1/2006 00:00:00):<? print_r($infos2) ?><br />
getdate-gmmktime <?php echo -$diff ?>: (1/1/2006 00:00:00):<? print_r($infos3) ?><br />
<hr />
getdate-mktime: (now):<? print_r($today1) ?><br />
getdate-gmmktime: (now):<? print_r($today2) ?><br />
getdate-gmmktime <?php echo -$diff ?>: (now):<? print_r($today3) ?><br />
</pre>
