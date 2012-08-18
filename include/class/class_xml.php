<?php
/*
--------------------------------------------------------------------------------
     File:  class_xml.php

    Class:  XML PARSER
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-01-17

   Syntax:  new xml();
            ->load_file(File);
            ->get_album();
            ->get_event();
  Require: [none]

  Comment:
--------------------------------------------------------------------------------
*/

class xml
{
	var $xml; // Object
	var $xml_array = array();
	var $xml_head;
	var $rss_head;
	var $rss_channel;

	function xml()
	{
		global $eps_config;
		$this->xml_head = '<?xml version="1.0" encoding="UTF-8"?>';
		$this->rss_head = '<rss version="2.0">';
		$this->rss_channel = array(
			'title' => $eps_config['title'],
			'link' => $eps_config['base_url'],
			'description' => $eps_config['desc'],
			'copyright' => $eps_config['copyright'],
			'language' => 'vn'
		);
	}

	// XML form file
	function load_file($file)
	{
		if ((strpos($file, 'tp://') !== false) || is_file($file))
		{
			if (function_exists('simplexml_load_file'))
			{
				$this->xml = (is_readable($file)) ? simplexml_load_file($file) : simplexml_load_string(urlfile_get_contents($file));
				$this->xml_array = $this->xml2array($this->xml);
			}
			else
			{
				$this->xml_array = $this->xml2array_2((is_readable($file)) ? file_get_contents($file) : urlfile_get_contents($file));
			}
			return true;
		}
		else
		{
			$this->xml_array = array();
			return false;
		}
	}
	
	// XML to Array
	function xml2array($xml)
	{
		if (get_class($xml) == 'SimpleXMLElement')
		{
			$attributes = $xml->attributes();
			foreach($attributes as $k => $v)
			{
				if ($v)
					$a[$k] = (string) $v;
			}
			$x = $xml;
			$xml = get_object_vars($xml);
		}

		if (is_array($xml))
		{
			if (count($xml) == 0)
				return (string) $x;	// for CDATA
			foreach($xml as $key => $value)
				$r[$key] = $this->xml2array($value);
			if (isset($a))
				$r['@'] = $a;	// Attributes
			return $r;
		}
		return (string) $xml;
	}

	function xml2array_2($xml_data)
	{
		// parse the XML datastring
		$xml_parser = xml_parser_create();
		xml_parser_set_option($xml_parser, XML_OPTION_SKIP_WHITE, true);
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
		xml_parse_into_struct($xml_parser, $xml_data, $vals, $index);
		xml_parser_free($xml_parser);
	
		$params = array();
		$ptrs[0] = &$params;  
		foreach ($vals as $xml_elem)
		{
			$pre_level = $xml_elem['level'] - 1;
			switch ($xml_elem['type'])
			{
				case 'open':
					$tag_or_id = (array_key_exists('attributes', $xml_elem) && isset($xml_elem['attributes']['id'])) ? $xml_elem['attributes']['id'] : $xml_elem['tag'];
					if ($pre_level > 0)
					{
						$ptrs[$pre_level][$tag_or_id][] = array();
						$ptrs[$pre_level + 1] = &$ptrs[$pre_level][$tag_or_id][count($ptrs[$pre_level][$tag_or_id])-1];
					}
					else
					{
						$ptrs[$pre_level + 1] = &$ptrs[$pre_level][$tag_or_id];
					}
					break;
				case 'complete':
					$ptrs[$pre_level][$xml_elem['tag']] = (isset ($xml_elem['value'])) ? $xml_elem['value'] : '';
					break;
			}
	   }

	   sort($params);
	   return ($this->_standardize_array($params[0]));
	}

	function _standardize_array($array)
	{
		if (is_array($array))
		{
			foreach ($array as $k => $v)
			{
				if (is_array($v) && count($v) == 1 && isset($v[0]))
					$r[$k] = $this->_standardize_array($v[0]);
				else
 					$r[$k] = $v;
			}
			return $r;
		}
		else
			return $array;
	}

	/* Get Album Form XML
	Array
	(
	    [2005-tamdao] => Array
	        (
	            [img] => tamdao05_050_t.jpg
	            [name] => Tam Dao 2005
	            [desc] => abc.
	        )
	
	    [2004-aovua] => Array
	        (
	            [img] => aovua2004_19_t.jpg
	            [name] => Ao Vua 2004
	            [desc] => cde.
	        )
	
	)
	*/
	function get_album()
	{
		if (!empty($this->xml_array['album']))
		{
			if (isset($this->xml_array['album'][0]) && is_array($this->xml_array['album'][0]))
			{
				foreach ($this->xml_array['album'] as $v)
				{
					$albums[$v['dir']]['img'] = $v['img'];
					$albums[$v['dir']]['name'] = $v['name'];
					$albums[$v['dir']]['desc'] = $v['desc'];
				}
			}
			else
			{
				$albums[$this->xml_array['album']['dir']]['img'] = $this->xml_array['album']['img'];
				$albums[$this->xml_array['album']['dir']]['name'] = $this->xml_array['album']['name'];
				$albums[$this->xml_array['album']['dir']]['desc'] = $this->xml_array['album']['desc'];
			}
			return $albums;
		}
		else
			return array();
	}


	/* Get Even For Calendar
	Array
	(
	    [20060222] => aaaaaa
	    [20060223] => bbbbbb
	    [20060224] => cccccc
	)
	*/
	function get_event()
	{
		if (!empty($this->xml_array['event']))
		{
			if (isset($this->xml_array['event'][0]) && is_array($this->xml_array['event'][0]))
			{
				foreach ($this->xml_array['event'] as $v)
					$events[$v['date']] = $v['desc'];
			}
			else
				$events[$this->xml_array['event']['date']] = $this->xml_array['event']['desc'];

			return $events;
		}
		else
			return array();
	}

	function get_rss()
	{
		return (!empty($this->xml_array['channel'])) ? $this->xml_array['channel'] : array();
	}
	
	/* Generate RSS
		$ITEMS: assoc_array([title],[link],[desc])
	*/
	function gen_rss($items)
	{
		$rss = $this->xml_head."\r\n";
		$rss .= $this->rss_head."\r\n";
		$rss .= '<channel>'."\r\n";
		$rss .= $this->_transform($this->rss_channel);

		foreach ($items as $v)
		{
			$rss .= '<item>'."\r\n";
			$rss .= $this->_transform($v);
			$rss .= '</item>'."\r\n";
		}
		$rss .= '</channel>'."\r\n".'</rss>';
		return $rss;
	}

	function _transform($items)
	{
		$trans = '';
		foreach($items as $k => $v)
		{
			$trans .= (is_array($items[$k])) ? $this->_transform($items[$k]) : "<$k>$v</$k>\r\n";
		}
		return $trans;
	}

	function data_reset()
	{
		$this->xml_array = array();
	}
}

?>
