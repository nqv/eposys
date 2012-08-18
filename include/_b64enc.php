<?php
/*
--------------------------------------------------------------------------------
     File:  b64enc.php

    Class:  BASE64ENCODE FILE
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-01-??

  Comment:  Define PNG Array
--------------------------------------------------------------------------------
*/

$srcdir = 'a';
function pngarray()
{
	global $srcdir, $delext;
	if (!preg_match('#\/$#', $srcdir))
		$srcdir .= '/';
	//Check
	if (!(is_readable($srcdir)))
	{
		echo '<strong>Error. Source Directory '.$srcdir.' is not readable.</strong>';
		exit;
	}

	$dir = dir($srcdir);
	$show = '$png = array(';
	while($name = $dir->read())
	{
		$file = $srcdir.$name;
		if(is_file($file))
		{
			$handle = fopen($file,'r');
			$src = fread($handle, filesize($file));	
			fclose($handle);
			$name = preg_replace('#\..+?$#', '', $name);
			$show .= "\n\t".'\''.$name.'\' => \''.base64_encode($src).'\',';
		}
	}
	$dir->close();
	$show = preg_replace('#,$#', '', $show)."\n".');';
	return $show;
}
echo pngarray();
