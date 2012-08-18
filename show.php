<?php
/*
--------------------------------------------------------------------------------
     File:  show.php

     Unit:  SHOW OFF
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2005-12-??

  Comment:  Individual show

--------------------------------------------------------------------------------
*/
if (!defined('IN_EPS'))
	exit;

$module = (!empty($_GET['eps'])) ? $_GET['eps'] : '';

?>

	<div class="show">

<?php
if ($module == 'album')
{
	$album = (!empty($_GET['album'])) ? urldecode($_GET['album']) : '';
	$pic = (!empty($_GET['pic'])) ? urldecode($_GET['pic']) : '';
	echo '<img src="'.((!empty($album) && !empty($pic)) ? EPS_ALBUM_DIR.htmlspecialchars($album).'/'.htmlspecialchars($pic) : './image/noimg.png').'" />';
}
?>

	</div>
