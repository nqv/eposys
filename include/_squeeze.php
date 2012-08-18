<?php
/*
--------------------------------------------------------------------------------
     File:  _squeeze.php
     Unit:  Code Squeezer
   Author:  Quoc Viet [aFeLiOn] (Based on xajax)
    Begin:  2006-02-10
--------------------------------------------------------------------------------
*/
$source_dir = 'd:/viet/web/eposys/js';
$target_dir = 'd:/server/htdocs/eposys/js';
$allows = array('js');


if (!is_dir($target_dir))
	mkdir($target_dir, 0777);
else if (!is_writeable($target_dir))
	chmode($target_dir, 0777);

//Scan Directory
$dir = dir($source_dir);
while ($filename = $dir->read())
{
	$old_file = $source_dir.'/'.$filename;
	if (is_file($old_file))
	{
		$ext = explode('.', $filename);
		$ext = strtolower($ext[count($ext) - 1]);

		if (in_array($ext, $allows))
		{
			$new_file = $target_dir.'/'.$filename;
			squeeze($old_file, $new_file);
			echo $filename.'<br />'."\n";
		}
	}
}
$dir->close();

function squeeze($source_file, $target_file)
{
	if (is_file($source_file))
		file_put_contents($target_file, js_squeeze(file_get_contents($source_file)));
	else
		exit('File not exists');
}

function js_squeeze($s)
{
	//remove windows cariage returns
	$s = str_replace("\r", '', $s);
	
	//array to store replaced literal strings
	$literal_strings = array();
	
	//explode the string into lines
	$lines = explode("\n", $s);

	//loop through all the lines, building a new string at the same time as removing literal strings
	$clean = '';
	$inComment = false;
	$literal = '';
	$inQuote = false;
	$escaped = false;
	$quoteChar = '';

	for($i=0; $i<count($lines); $i++)
	{
		$line = $lines[$i]."\n";
		$inNormalComment = false;
		$have_regex = (preg_match('#[=\(]\s*/.*[^(\\)]/#', $line)) ? true : false;

		//loop through line's characters and take out any literal strings, replace them with ___i___ where i is the index of this string
		for($j = 0; $j < strlen($line); $j++)
		{
			$b = ($j > 0) ? substr($line, $j - 1, 1) : '';
			$c = substr($line, $j, 1);
			$d = ($j < strlen($line) - 1) ? substr($line, $j, 2) : '';
	
			//look for start of quote
			if(!$inQuote && !$inComment)
			{
				//is this character a quote or a comment
				if(($c == '"' || $c == "'" || ($c == '/' && $have_regex && ($b != '/') && ($d != '//' && $d != '/*'))) && !$inComment && !$inNormalComment)
				{
					$inQuote = true;
					$inComment = false;
					$escaped = false;
					$quoteChar = $c;
					$literal = $c;
				}
				else if($d == '/*' && !$inNormalComment)
				{
					$inQuote = false;
					$inComment = true;
					$escaped = false;
					$quoteChar = $d;
					$literal = $d;	
					$j++;	
				}
				else if($d == '//') //ignore string markers that are found inside comments
				{
					$inNormalComment = true;
					$clean .= $c;
				}
				else
				{
					$clean .= $c;
				}
			}
			else //allready in a string so find end quote
			{
				if($c == $quoteChar && !$escaped && !$inComment)
				{
					$inQuote = false;
					$literal .= $c;
	
					//subsitute in a marker for the string
					$clean .= '___'.count($literal_strings).'___';
	
					//push the string onto our array
					array_push($literal_strings, $literal);
				}
				else if($inComment && $d == '*/')
				{
					$inComment = false;
					$literal .= $d;
	
					//subsitute in a marker for the string
					$clean .= '___' . count($literal_strings) . '___';
	
					//push the string onto our array
					array_push($literal_strings, $literal);
	
					$j++;
				}
				else if ($c == '\\' && !$escaped)
					$escaped = true;
				else
					$escaped = false;
				$literal .= $c;
			}
		}
// 		if ($inComment)
// 			$literal .= "\n";
// 
// 		$clean .= "\n";
	}
	
	//explode the clean string into lines again
	$lines = explode("\n", trim($clean));
	$sq_lines = array();
	//now process each line at a time
	for($i=0; $i<count($lines); $i++)
	{
 		$line = $lines[$i];

		//remove comments
		$line = preg_replace("/\/\/(.*)/", "", $line);

		//remove all whitespace with a single space
		$line = preg_replace("/\s+/", " ", $line);

		//strip leading and trailing whitespace
		$line = trim($line);
	
		//remove any whitespace that occurs after/before an operator
		$line = preg_replace("/\s*([!\}\{;,&=\|\-\+\*\/\)\(:])\s*/", "\\1", $line);

		// need this to remove first empty line if file encode in utf8 with BOM
		if (!empty($line))
			array_push($sq_lines, $line);
	}
	//implode the lines
	$s = implode("\n", $sq_lines);

	$skips = array();
	// remove multi-line comments except first
	for($i=0; $i<count($literal_strings); $i++)
	{
		if (preg_match('#^/\*.*\*/$#s', $literal_strings[$i]) && $i != 0)
			$skips[] = '___'.$i.'___';
	}
	$s = str_replace($skips, '', $s);

	$s = preg_replace('#\n{2,}#s', "\n", $s);	// Empty line
	$s = preg_replace('#[ \t\n]*\{[ \t\n]*#s', '{', $s);	// Line & Space Around {
	$s = preg_replace('#[ \t\n]+\}#s', '}', $s);	// Line & Space Before }
	$s = preg_replace('#\}[ \t\n]+\}#s', '}}', $s);	// Line Between } }
	$s = preg_replace('#\}[ \t\n]+\}#s', '}}', $s); // x2
	$s = preg_replace('#;\n+#s', ';', $s);	// Line After ;

	//finally loop through and replace all the literal strings:
	for($i=0; $i < count($literal_strings); $i++)
	{
		if (!in_array('___'.$i.'___', $skips))
			$s = str_replace('___'.$i.'___', (preg_match('#^/\*.*\*/$#s', $literal_strings[$i]) && $i != 0) ? '' : $literal_strings[$i], $s);
	}
	return $s;
}

function dmp()
{
	echo '<pre>';
	$num_args = func_num_args();
	for ($i = 0; $i < $num_args; $i++)
	{
		print_r(func_get_arg($i));
		echo "\n\n";
	}
	echo '</pre>';
	exit;
}
?>
