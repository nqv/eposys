<?php
/*
--------------------------------------------------------------------------------
     File:  class_bbcode.php

    Class:  BBCODE
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-01-05

   Syntax:  new bbcode();
            ->format(Text, No_smiley = 0);
  Require:  [none]

  Comment:
--------------------------------------------------------------------------------
*/

class bbcode
{
 	var $emoticons = array(
		':)' => 'smile.png',
		':(' => 'sad.png',
		';)' => 'wink.png',
		':D' => 'biggrin.png',
		'B-)' => 'cool.png',
		':))' => 'laugh.png',
		':((' => 'cry.png',
		'^_^' => 'happy.png',
		':O_o:' => 'blink.png',
		':-P' => 'tongue.png',
		'/:|' => 'huh.png',
		'8-|' => 'rolleyes.png',
		':-O' => 'ohmy.png',
		'X-(' => 'mad.png',
		':-&' => 'sick.png',
		':-S' => 'wacko.png',
		':sigh:' => 'sigh.png',
		'X-.' => 'pinch.png',
		':-*' => 'whistling.png',
		'|-|' => 'sleep.png',
		':w00t:' => 'w00t.png',
		':wub:' => 'wub.png'
	);

	var $patterns = array(
		'#\[b\](.+?)\[/b\]#si',
		'#\[i\](.+?)\[/i\]#si',
		'#\[u\](.+?)\[/u\]#si',
		'#\[s\](.+?)\[/s\]#si',
		'#\[left\](.+?)\[/left\]#si',
		'#\[center\](.+?)\[/center\]#si',
		'#\[right\](.+?)\[/right\]#si',
		'#\[color=([a-z]*|\#?[0-9a-f]{6})](.*?)\[/color\]#si',
		'#\[email\]([^ \"\[]+?@[^ \"\[]+?)\[/email\]#i',
		'#\[email=([^ \"\[]+?@[^ \"\[]+?)\](.+?)\[/email\]#i',
		'#\[img\](.+?)\[/img\]#i',
		'#\[url\]([\w]+?://[^ \"\[]*?)\[/url\]#i',
		'#\[url\]([^ \"\[]*?)\[/url\]#i',
		'#\[url=([\w]+?://[^ \"\[]*?)\](.+?)\[/url\]#i',
		'#\[url=([^ \"\[]*?)\](.+?)\[/url\]#i',
		'#\[quote\](.+?)\[/quote\]#si',
		'#\[quote=(\'|"|&quot;)*(.+?)(\'|"|&quot;)*\](.+?)\[/quote\]#si',
		'#\[code\](.+?)\[/code\]#si',
		'#\[size=([1-9]+)\](.+?)\[/size\]#si'
	);

	var $replaces = array(
		'<strong>$1</strong>',
		'<em>$1</em>',
		'<u>$1</u>',
		'<strike>$1</strike>',
		'<div align=left>$1</div>',
		'<div align=center>$1</div>',
		'<div align=right>$1</div>',
		'<span style="color:$1">$2</span>',
		'<a href="mailto:$1">$1</a>',
		'<a href="mailto:$1">$2</a>',
		'<img src="$1" alt="" class="postimg" />',
		'<a href="$1" target="_blank">$1</a>',
		'<a href="http://$1" target="_blank">$1</a>',
		'<a href="$1" target="_blank">$2</a>',
		'<a href="http://$1" target="_blank">$2</a>',
		'<p><blockquote><div class="bl_quote">$1</div></blockquote></p>',
		'<p><blockquote><div class="bl_quote"><div class="bl_title">$2:</div>$4</div></blockquote></p>',
		'<p><code><div class="bl_code">$1</div></code></p>',
		'<span style="font-size: 1.$1em">$2</span>'
	);

	var $plaintext = array(
		'$1',
		'$1',
		'$1',
		'$1',
		'$1',
		'$1',
		'$1',
		'$2',
		'$1',
		'$1',
		'[IMAGE]',
		'[LINK]',
		'[LINK]',
		'[LINK]',
		'[LINK]',
		'[QUOTE]',
		'[QUOTE]',
		'[CODE]',
		'$2'
	);

	// Smiley
	function _smiley_parse($text)
	{
		foreach ($this->emoticons as $smiley_txt => $smiley_img)
		{
			$text = preg_replace('#(?<=.\W|\W.|^\W)'.preg_quote($smiley_txt, '#').'(?=.\W|\W.|\W$)#m', '$1<img src="image/emoticon/'.$smiley_img.'" class="emoticon" alt="'.substr($smiley_img, 0, strrpos($smiley_img, '.')).'" />$2', $text);
		}
		return $text;
	}
	
	// BBCode
	function _bbcode_parse($text)
	{
		return preg_replace($this->patterns, $this->replaces , $text); 
	}
	
	// Format Text
	function format($text, $no_smiley = 0)
	{
		// Special HTML Char
		$text = htmlspecialchars($text);
	  
		// Linefeed and Multispace 
		$text = str_replace(array("\n", "\t", '  ', '  '), array('<br />', '&nbsp; &nbsp; ', '&nbsp; ', ' &nbsp;'), $text);
	
		// Smiley & BBCode
		if ($no_smiley == 0)
			$text = $this->_smiley_parse($text);
		$text = $this->_bbcode_parse($text);

		return $text;
	}
	
	function clean($text)
	{
		$text = htmlspecialchars($text);
		return preg_replace($this->patterns, $this->plaintext , $text); 
	}
}
?>
