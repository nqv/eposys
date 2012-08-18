<?php
/*
--------------------------------------------------------------------------------
     File:  class_extractor.php

    Class:  ARCHIVE EXTRACTOR
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-03-14

   Syntax:  new extractor(Library_dir);
            ->extract(File, Destination);

  Require:  PhpConcept Library - Tar Module
            PhpConcept Library - Zip Module
            + Contant: EPS_LIBRARY_DIR
  Comment:
--------------------------------------------------------------------------------
*/

class archive
{
	function extract($file, $dest = './')
	{
		$result = null;

		if (preg_match('#zip$#i', $file))
      	$result = $this->extract_zip($file, $dest);
		else if (preg_match('#(tar|gz|tar.gz|tgz)$#i', $file))
			return 0;
			//$result = $this->extract_tar($file, $dest);

		return $result;
	}

	function extract_tar($file, $dest = './')
	{
		$g_pcltar_lib_dir = $this->lib_dir;
		require_once EPS_LIBRARY_DIR.'pcltar.lib.php';

		return PclTarExtract($file, $dest);
	}

	function extract_zip($file, $dest = './')
	{
		$dest = rtrim($dest, '/').'/';
		require_once EPS_LIBRARY_DIR.'pclzip.lib.php';
  		$zip = new PclZip($file);
		return $zip->extract(PCLZIP_OPT_PATH, $dest,
		                        PCLZIP_OPT_SET_CHMOD, 0777);
	}
}
?>
