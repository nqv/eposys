<?php
/*
--------------------------------------------------------------------------------
     File:  news.php

   Module:  NEWS
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-01-02

  Comment:
--------------------------------------------------------------------------------
*/

if (!defined('IN_EPS'))
	exit;

// GET
$p = eps_get_var('p', 1, true);
$nid = eps_get_var('nid', 0, true);
$type = eps_get_var('type', '');

$max_length = 600;

// Class BBCode
$epsclass->load_class('class_bbcode');

// Brief News
if (isset($tiny_method) && $tiny_method == 'news')
{
	$result = $epsclass->db->vselect(TBL_NEWS, true, "WHERE type=2 ORDER BY `post_time` DESC LIMIT 0,20", true);
	if ($epsclass->db->num_rows($result))
	{
?>
	<ul>
<?php
		while ($cur_news = $epsclass->db->fetch_assoc($result))
		{
			if (eps_strlen($cur_news['content']) > $max_length)
				$cur_news['content'] = eps_truncate($cur_news['content'], $max_length);
			echo '<li>'.auto_gen_link('index.php?eps=news&amp;nid='.$cur_news['id'], $cur_news['title'], $epsclass->bbcode->clean($cur_news['content']), true, true, true).'</li>'."\n";
		}
?>
	</ul>
<?php
		$epsclass->db->free_result($result);
	}
	else
		$smarty->display('empty.tpl');
}

// Main News
else
{
	// Class Paginate
	$epsclass->load_class('class_paginate');
	
	if ($nid < 1)
	{
		$sqls = array(
		"SELECT COUNT(id) FROM ".TBL_NEWS." WHERE type=1",
		"SELECT u1.username AS poster,u2.username AS editor, n.* FROM ".TBL_NEWS." n LEFT JOIN ".TBL_USER." u1 ON n.poster_id=u1.id LEFT JOIN ".TBL_USER." AS u2 ON n.edit_by=u2.id WHERE n.type=1 ORDER BY n.post_time DESC"
		);
		$result = $epsclass->paginate->get_result($sqls, 'index.php', $p, 10);
	}
	else
	{
		$sql = "SELECT u1.username AS poster,u2.username AS editor,n.* FROM ".TBL_NEWS." n LEFT JOIN ".TBL_USER." u1 ON n.poster_id=u1.id LEFT JOIN ".TBL_USER." AS u2 ON n.edit_by=u2.id WHERE n.id='".$epsclass->db->escape($nid)."'";
		$result = $epsclass->db->query($sql) or error('Unable To fetch news', __FILE__, __LINE__, $epsclass->db->error());
	}
 
	if ($epsclass->db->num_rows($result))
	{
		$news = array();
		while ($cur_news = $epsclass->db->fetch_assoc($result))
		{
			// Truncate
			if (eps_strlen($cur_news['content']) > $max_length && $nid < 1)
			{
				$cur_news['content'] = eps_truncate($cur_news['content'], $max_length);
				$cur_news['title'] = auto_gen_link('index.php?nid='.$cur_news['id'], html_clean($cur_news['title']));
			}
			else
				$cur_news['title'] = html_clean($cur_news['title']);

			// BBCode
			$cur_news['content'] = $epsclass->bbcode->format($cur_news['content'], $cur_news['no_smiley']);

			// Author Or MODADMIN
			if (!empty($eps_user['id']) && ($cur_news['poster_id'] == $eps_user['id'] || IS_MODADMIN))
			{
				// Edit, Delete Link
				$cur_news['action'] = gen_editlink('index.php?eps=post&amp;nid='.$cur_news['id']);

				// Last edit by
				if (!empty($cur_news['edit_by']))
					$cur_news['content'] .= '<p class="postedit">'.$eps_lang['Last_edit_by'].': '.html_clean($cur_news['editor']).' &raquo; '.format_time($cur_news['edit_time']).'</p>';
			}
			$cur_news['desc'] = auto_gen_link('index.php?eps=list&amp;list=user&amp;uid='.$cur_news['poster_id'], html_clean($cur_news['poster']), '', true).' &raquo; '.format_time($cur_news['post_time']); 

			$news[] = $cur_news;
		}

		$smarty->assign('news_shows', $news);
		$smarty->assign('nid', $nid);
		$smarty->assign('pagination', $epsclass->paginate->gen_page_link());
		$smarty->display('module/news.tpl');

	}
	else
		$smarty->display('empty.tpl');
	
	$epsclass->db->free_result($result);
}

unset($sql, $news, $nid, $type, $max_length);
