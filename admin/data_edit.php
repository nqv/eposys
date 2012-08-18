<?php
/*
--------------------------------------------------------------------------------
     File:  data_edit.php

   Module:  DATA MODIFIER
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-03-29

  Comment:
--------------------------------------------------------------------------------
*/

if (!defined('IN_EPS') || !IS_MODADMIN || !IS_ADMIN)
	exit;

$data = eps_get_var('data', '');
$var = eps_get_var('var', '');

$data_file_allows = array('event', 'poll');

if (empty($data) || !in_array($data, $data_file_allows))
	return;

$data_file = file_get_contents(EPS_DATA_DIR.$data);
if (!is_file($data_file))
	return;

if (isset($_POST['form_sent']) && $_POST['form_sent'] == 'data_edit')
{
	$content = str_replace("\r", '', trim($_POST['content']));
	if (!empty($var))
	{
		$c_lines = explode("\n", $content);
		$contents = array();
		foreach ($c_lines as $line)
		{
			$tmp = explode('|', $line);
			if (count($tmp) == 2)
				$contents[html_clean(trim($tmp[0]))] = html_clean(trim($tmp[1]));
		}
		$content = '<?php'."\n".'$'.$var.' = '.var_export($contents, true).';'."\n".'?>';
	}
	create_file($content, $data_file, true);
	redirect('index.php?eps=data_edit&amp;data='.$data.((!empty($var)) ? '&amp;var='.$var : ''), $eps_lang['Redirect_data_edit']);
}

if (empty($var))
	$data_content = html_clean(file_get_contents($data_file));
else
{
	// Class created here to prevents variable hack
	class temp_data_class
	{
		var $d_content;
		function tmp_data($d_file, $var)
		{
			require $d_file;

			$d_content = '';
			if (!isset($$var))
				return '';

			eval('foreach ($'.$var.' as $k => $v) $d_content .= $k." | ".$v."\n";');

			$this->d_content = html_clean($d_content);
		}

		function get()
		{
			return $this->d_content;
		}
	};
	$tmp_data = new temp_data_class($data_file, $var);
	$data_content = $tmp_data->get();
}
$smarty->assign('form_tag', auto_gen_form('index.php?eps=data_edit&amp;data='.$data.((!empty($var)) ? '&amp;var='.$var : ''), 'data_edit', true));
$smarty->assign('content', $data_content);

unset($data_file, $data_file_allows, $data_content);
$smarty->display('admin/data_edit.tpl');
?>
