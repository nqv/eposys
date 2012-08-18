<?php
/*
--------------------------------------------------------------------------------
     File:  index.php

     Unit:  INDEX
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2005-12-29

  Comment:  EPS wrap

--------------------------------------------------------------------------------
*/

define('EPS_ROOT', './');
require EPS_ROOT.'include/common.php';

$eps_title = html_clean($eps_config['title']);
require EPS_ROOT.'header.php';

if (@$_GET['mode'] == 'show')
{
	require EPS_ROOT.'show.php';
}
else
{
// Extra
?>
<div id="eps_header"><h1><?php echo $eps_title ?></h1><span><?php echo $eps_config['desc'] ?></span></div>
<?php
	require EPS_ROOT.'eps_left.php';
	require EPS_ROOT.'eps_right.php';
?>
<div id="eps_main">
	<div id="eps_main_content" class="eps_inmain">
<?php
	require EPS_ROOT.'module.php';
?>
	</div>
</div>

<div id="eps_footer"><p><?php echo $eps_config['copyright'] ?></p><p><?php echo $eps_config['info'] ?></p></div>
<?php
}
require EPS_ROOT.'footer.php';
?>
