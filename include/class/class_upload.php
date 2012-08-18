<?php
/*
--------------------------------------------------------------------------------
     File:  class_upload.php

    Class:  UPLOAD
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-01-22

   Syntax:  new upload(Directory, Extensions_Allowed[Array], Max_File_Size);
            ->up(File);
  Require:
   + Variable: $eps_user; $eps_lang;

  Comment:
--------------------------------------------------------------------------------
*/
class upload
{
	var $dir = '';
	var $maxsize;
	var $disallow_exts = array('php', 'php3', 'php4', 'php5', 'phps');
	var $allow_exts = array();
	var $errors = array();
	var $uploaded_infos = array();

	function upload($dir, $allow_exts, $maxsize = 5242880)
	{
		$dir = rtrim($dir, '/').'/';
		$this->create_dir($dir);
		$this->dir = $dir;
		$this->allow_exts = $allow_exts;
		$this->maxsize = $maxsize;
		$upload_max_filesize = floor($maxsize / 1048576); // MB
		@ini_set('file_uploads', '1');
		@ini_set('upload_max_filesize', $upload_max_filesize.'M');
		@ini_set('post_max_size', ($upload_max_filesize + 2).'M');
	}
  
	// Get Extendion
	function get_ext($file)
	{
		return strtolower(ltrim(strrchr($file, '.'), '.'));
	}

	// Get Name
	function get_name($file)
	{
		return substr($file,0, strrpos($file, '.'));
	}

	// Vadidate File
	function chk_file($name, $size)
	{
		global $eps_lang;
		if (preg_match('#^[a-z0-9\-_\(\)\[\]\. ]+\.[a-z0-9]{1,5}$#si', $name))
		{
			$ext = $this->get_ext($name);
			if (!in_array($ext, $this->disallow_exts))
			{
				if (in_array($ext, $this->allow_exts))
				{
					if ($size <= $this->maxsize)
						return true;
					else
					{
						$this->errors[] = $eps_lang['File_too_big'];
						return false;
					}
				}
				else
				{
					$this->errors[] = $eps_lang['Allow_file_type'].': '.implode(', ', $this->allow_exts);
					return false;
				}
			}
			else
			{
				$this->errors[] = $eps_lang['File_type_disallowed'];
				return false;
			}
		}
		else
		{
			$this->errors[] = $eps_lang['File_name_invalid'];
			return false;
		}
	}

	// Create Folder If No Exist
	function create_dir($dir)
	{
		if (!is_dir($dir))
		{
			if (mkdir($dir, 0777))
				return true;
			else
				return false;
		}
		else
			return true;
	}

	// Upload
	function up($file)
	{
		global $eps_user, $eps_lang;
		$file_name = $_FILES[$file]['name'];
		if (!empty($file_name))
		{
			$file_size = $_FILES[$file]['size'];
			$file_mime = (!empty($_FILES[$file]['type'])) ? $_FILES[$file]['type'] : $this->mime_detect($this->get_ext($file_name));
			$file_error = $_FILES[$file]['error'];
			if ($file_error == UPLOAD_ERR_OK)
			{
				if ($this->chk_file($file_name, $file_size))
				{
					$tmp_file = $_FILES[$file]['tmp_name'];
					if (is_uploaded_file($tmp_file))
					{
						$new_name = preg_replace('#\s+#ui', '_', $this->get_name($file_name)).'_'.$eps_user['id'].'.'.$this->get_ext($file_name);
						$uploaded_file = $this->dir.$new_name;
						if (!file_exists($uploaded_file))
						{
							if (move_uploaded_file($tmp_file, $uploaded_file))
							{
								$this->uploaded_infos['name'] = $new_name;
								$this->uploaded_infos['size'] = $file_size;
								$this->uploaded_infos['type'] = $file_mime;
								return true;
							}
							else
							{
								$this->errors[] = $eps_lang['File_not_saved'];
								return false;
							}
						}
						else
						{
							$this->errors[] = $eps_lang['File_exist'];
							return false;
						}
					}
					else
					{
						$this->errors[] = $eps_lang['File_not_upload'];
						return false;
					}
				}
				else
				{
					return false;
				}
			}
			else
			{
				$this->errors[] = $eps_lang['File_upload_error'].': '.$file_name;
				return false;
			}
		}
		else
		{
			$this->errors[] = $eps_lang['No_file_upload'].': '.$file_name;
			return false;
		}
	}

	// Mime Detect
	function mime_detect($ext='')
	{
		$mimes = array(
			'rtf' => 'text/richtext',
			'html' => 'text/html',
			'htm' => 'text/html',
			'aiff' => 'audio/x-aiff',
			'iff' => 'audio/x-aiff',
			'basic' => 'audio/basic',
			'wav' => 'audio/wav',
			'gif' => 'image/gif',
			'jpg' => 'image/jpeg',
			'jpeg' => 'image/pjpeg',
			'tif' => 'image/tiff',
			'png' => 'image/x-png',
			'xbm' => 'image/x-xbitmap',
			'bmp' => 'image/bmp',
			'xjg' => 'image/x-jg',
			'emf' => 'image/x-emf',
			'wmf' => 'image/x-wmf',
			'avi' => 'video/avi',
			'mpg' => 'video/mpeg',
			'mpeg' => 'video/mpeg',
			'ps' => 'application/postscript',
			'b64' => 'application/base64',
			'macbinhex' => 'application/macbinhex40',
			'pdf' => 'application/pdf',
			'xzip' => 'application/x-compressed',
			'zip' => 'application/x-zip-compressed',
			'gzip' => 'application/x-gzip-compressed',
			'java' => 'application/java',
			'msdownload' => 'application/x-msdownload'
			);

		foreach ($mimes as $type => $mime )
		{
			if ($ext == $type)
				return $mime;
		}
		return 'application/octet-stream';
	}
}
?>
