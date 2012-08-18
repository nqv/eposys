<?php
/*
--------------------------------------------------------------------------------
     File:  album.php

   Module:  ALBUM
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-01-20

  Comment:
--------------------------------------------------------------------------------
*/

if (!defined('IN_EPS'))
	exit;

// GET
$album = urldecode(eps_get_var('album', ''));

// XML Object
$epsclass->load_class('class_xml');
$epsclass->xml->load_file(EPS_DATA_DIR.'k48htd.xml');

// Album In XML file
$album_infos = $epsclass->xml->get_album();

// Album In Album-Directory
$albums = get_stuff('album');

// All Album
if (empty($album))
{
	$album_shows = array();
	foreach ($albums as $cur_album)
	{
		if (!empty($album_infos[$cur_album]))
		{
			$cur_album_info = '<img src="'.EPS_ALBUM_DIR.html_clean($cur_album).'/'.html_clean($album_infos[$cur_album]['img']).'" />';
			$cur_album_info .= '<h3>'.html_clean($album_infos[$cur_album]['name']).'</h3>';
			$cur_album_info .= '<p>'.html_clean($album_infos[$cur_album]['desc']).'</p>';	
		}
		else
		{
			$cur_album_info = '<img src="image/noimg.png" />';
			$cur_album_info .= '<h3>'.htmlspecialchars($cur_album).'</h3>';
		}
		$album_shows[] = auto_gen_link('index.php?eps=album&amp;album='.urlencode($cur_album), $cur_album_info, '', true);
	}
	
	$smarty->assign('album_shows', $album_shows);
}

// Thumbnail
else
{
	if (!is_dir(EPS_ALBUM_DIR.$album))
	{
		return;
	}
	// In Album-Directory
	$thumbs = get_stuff('thumb', $album);

	// No Thumbs
	if (empty($thumbs))
	{
?>
		<div class="showalbum">
			<img src="./image/noimg.png" />
		</div>
<?php
		return;
	}
	else
	{
		$prefix = 'index.php?eps=album&amp;album=';
		$album_values = array();
		foreach ($albums as $v)
		{
			$album_values[$prefix.urlencode($v)] = (!empty($album_infos[$v])) ? $album_infos[$v]['name'] : $v;
		}
	
		$tpl_jump = array(
			'name' => $eps_lang['Album'],
			'onchange' => (eps_use_ajax()) ? 'vQ(this.options[this.selectedIndex].value)' : 'window.location=this.options[this.selectedIndex].value',
			'value' => $album_values,
			'selected' => $prefix.$album
		);

		$smarty->assign('tpl_jump', $tpl_jump);
		$smarty->assign('album_name', html_clean((!empty($album_infos[$album])) ? $album_infos[$album]['name'] : $album));
		$smarty->assign('album_pics', thumb2pic($thumbs));
		$smarty->assign('album_dir', EPS_ALBUM_DIR.html_clean($album).'/');
	}
}

$smarty->assign('album', $album);
$smarty->display('module/album.tpl');

?>
