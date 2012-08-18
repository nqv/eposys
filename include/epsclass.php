<?php
/*
--------------------------------------------------------------------------------
     File:  eps_class.php

    Class:  EPS ALL CLASS
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-01-25

   Syntax:  new epsclass();
            ->load_class(array[FileName, ClassName, ObjectName], Argument = '',... )

  Comment:  FileName no contains extension(".php")

--------------------------------------------------------------------------------
*/

class epsclass
{
	var $class_loaded = array();

	// Load Class
	function load_class($infos, $arg = '')
	{
		// Class Name
		if (!is_array($infos))
		{
			$file_name = $infos;
			$class_name = preg_replace('#^class_(.*)#si', '$1', $file_name);
			$obj_name = $class_name;
		}
		else
		{
			$file_name = $infos[0];
			$class_name = $infos[1];
			if (count($infos) == 2)
				$obj_name = $class_name;
			else if (count($infos) == 3)
				$obj_name = $infos[2];
			else
				return false;
		}
		
		if (isset($this->class_loaded[$class_name][$obj_name]))
			return true;
		else
		{
			require EPS_CLASS_DIR.$file_name.'.php';
	
			// Argument
			$num_arg = func_num_args();
			$args = func_get_args();
			$parameter = '';

			if ($num_arg > 2)
			{
				for ($i = 1; $i < count($args); $i++)
				{
					$parameter .= '$args['.$i.']';
					if ($i != (count($args)-1))
						$parameter .= ',';
				}
			}
			else if ($num_arg == 2)
				$parameter = '$args[1]';
			else
				$parameter = '';

			eval('$this->'.$obj_name.' = new '.$class_name.'('.$parameter.');');
			if (isset($this->$obj_name))
			{
				$this->class_loaded[$class_name][$obj_name] = true;
				return true;
			}
			else
				exit('Can\'t load class "'.$class_name.'" in "'.EPS_CLASS_DIR.$file_name.'.php"');
		}
	}
}

?>
