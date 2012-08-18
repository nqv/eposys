<?php
/*
--------------------------------------------------------------------------------
     File:  class_image.php

    Class:  IMAGE HANDLING
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-06-04

   Syntax:  new validate();
            ->chk_length(String, Min_length, Max_length = null, Name);

  Require:
    + Function: pic2thumb();

  Comment:
--------------------------------------------------------------------------------
*/

class image
{
	var $file = '';
	var $format;
	var $image;
	var $width;
	var $height;
	var $new_image;
	var $errors = array();

	function open($file)
	{
		$infos = getimagesize($file);
		switch($infos[2])
		{
			case 1:
				$this->image = imagecreatefromgif($file);
				$this->format = 'gif';
				break;
			case 2:
				$this->image = imagecreatefromjpeg($file);
				$this->format = 'jpg';
				break;
			case 3:
				$this->image = imagecreatefrompng($file);
				$this->format = 'png';
				break;
			default:
				return false;
		}
		$this->file = $file;
		$this->width = $infos[0];
		$this->height = $infos[1];
	}

	function resize($new_max_size)
	{
		if ($this->file == '')
			return false;

		$scale = min($new_max_size / $this->width, $new_max_size / $this->height);
		$new_width = $this->width * $scale;
		$new_height = $this->height * $scale;

		$this->new_image = imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($this->new_image, $this->image, 0, 0, 0, 0, $new_width, $new_height, $this->width, $this->height);
		return true;
	}
	
	function save($destination)
	{
		if (!$this->new_image)
			return false;

		switch ($this->format)
		{
			case 'gif':
				imagegif($this->new_image, $destination);
				break;		
			case 'jpg':
				imagejpeg($this->new_image, $destination, 70);
				break;
			case 'png':
				imagepng($this->new_image, $destination);
				break;
		}
	}

	function create_thumb($file)
	{
		$this->open($file);
		$this->resize(50);
		$this->save(pic2thumb($file));
	}
}
?>
