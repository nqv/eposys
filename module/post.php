<?php
/*
--------------------------------------------------------------------------------
     File:  post.php
   Module:  POST
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-01-09
--------------------------------------------------------------------------------
*/

if (!defined('IN_EPS'))
	exit;

// No Guest
if ($eps_user['is_guest'])
{
	echo $eps_lang['Must_login'];
	return;
}

// GET
$nid = eps_get_var('nid', 0, true);
$action = eps_get_var('action', 'post');

// Class Validate
$epsclass->load_class('class_validate');

// Class Anti-flood
$epsclass->load_class('class_antiflood');

$epsclass->load_class('class_bbcode');

if (!in_array($action, array('post', 'edit', 'delete')))
	$action = 'post';

$news = array(
	'title' => '',
	'content' => '',
	'imgurl' => '',
	'type' => 1,
	'no_smiley' => 0
);

$errors = array();
$epsclass->validate->data_reset();

// Permision
if ($nid >= 1 && ($action == 'edit' || $action == 'delete'))
{
	// Fetch Post
	$result = $epsclass->db->vselect(TBL_NEWS, array('id','title','content','poster_id','imgurl','type','no_smiley'), $nid);
	if (!$epsclass->db->num_rows($result))
	{
		alert($eps_lang['Bad_request']);
		return;
	}
	else
	{
		// Check Author
		$news = $epsclass->db->fetch_assoc($result);
		$epsclass->db->free_result($result);

		if (!IS_MODADMIN && $news['poster_id'] != $eps_user['id'])
		{
			alert($eps_lang['No_permision']);
			return;
		}
	}
}

// Submit
if (isset($_POST['form_sent']))
{
	// Form Correct?
	if (($_POST['form_user'] != $eps_user['username'] && !IS_MODADMIN) || !in_array($action, array('post', 'edit', 'delete')))
	{
		alert($eps_lang['Bad_request']);
		return;
	}
	// If Delete
	else if ($action == 'delete' && $nid >= 1)
	{
		$epsclass->db->vdelete(TBL_NEWS, $nid);
		redirect('index.php', $eps_lang['Redirect_news_'.$action]);
		return;
	}

	// Clean POST
	$title = trim($_POST['req_title']);
	$content = eps_linebreak(trim($_POST['req_content']));
	$imgurl = trim($_POST['imgurl']);
	$type = intval($_POST['type']);
	$no_smiley = isset($_POST['no_smiley']) ? 1 : 0;

	// Anti-Flood
	if (!$epsclass->antiflood->verify('post'))
		$errors[] = $eps_lang['Flood_remain'].' '.$epsclass->antiflood->wait.' '.$eps_lang['Second'];

	else
	{
		$epsclass->validate->chk_length($title, 5, 99, $eps_lang['Title']);
		$epsclass->validate->chk_length($content, 50, 60000, $eps_lang['Content']);
	
		if ($type != 1 && $type != 2)
			$type = 2;
	}

	// Process
	if (empty($errors))
	{
		if (empty($epsclass->validate->errors))
		{
			if ($action == 'edit' && $nid >= 1)	// Update
			{
				$updates = array(
					'title' => $title,
					'content' => $content,
					'imgurl' => $imgurl,
					'edit_by' => $eps_user['id'],
					'edit_time' => time(),
					'type' => $type
				);
				$epsclass->db->vupdate(TBL_NEWS, $updates, $nid);
			}
			else if ($action == 'post')	// Insert
			{
				$inserts = array(
					'title' => $title,
					'content' => $content,
					'imgurl' => $imgurl,
					'poster_id' => $eps_user['id'],
					'post_time' => time(),
					'type' => $type
				);
				$epsclass->db->vinsert(TBL_NEWS, $inserts);
			}

			// Gen RSS
			$epsclass->load_class('class_xml');

			$result = $epsclass->db->query("SELECT u.username, n.id,n.title,n.content,n.post_time FROM ".TBL_NEWS." n LEFT JOIN ".TBL_USER." u ON n.poster_id=u.id ORDER BY post_time DESC LIMIT 15");

			$rss = array();
			while($cur_rss = $epsclass->db->fetch_assoc($result))
			{
				$rss[] = array(
					'title' => html_clean($cur_rss['title']),
					'link' => $eps_config['base_url'].'index.php?nid='.$cur_rss['id'],
					'description' => $epsclass->bbcode->format($cur_rss['content'], 600),
					'author' => html_clean($cur_rss['username']),
					'pubDate' => format_time($cur_rss['post_time'])
				);
			}
			$epsclass->db->free_result($result);
			create_file($epsclass->xml->gen_rss($rss), 'eps_news.xml', EPS_DATA_DIR);

			$epsclass->antiflood->update('post', 1);
			redirect('index.php', $eps_lang['Redirect_news_'.$action]);
		}
		else
		{
			$errors = $epsclass->validate->errors;
			$epsclass->validate->data_reset();
		}
	}
}

// For Select Box
$news['type'] = (isset($type)) ? $type : $news['type'];
$news['no_smiley'] = (isset($no_smiley)) ? $no_smiley : $news['no_smiley'];

if ($action == 'edit')
	$page_title = $eps_lang['Page_post_edit'];
else if ($action == 'delete')
	$page_title = $eps_lang['Page_post_delete'];
else
	$page_title = $eps_lang['Page_post'];

$req_fields = array(
	'title' => $eps_lang['Title'],
	'content' => $eps_lang['Content'],
);

$smarty->assign('js_lang', gen_jslang($req_fields));
$smarty->assign('emoticons', $epsclass->bbcode->emoticons);
$smarty->assign('action', $action);
$smarty->assign('page_title', $page_title);
$smarty->assign('error_show', (!empty($errors)) ? gen_alert($errors) : '');
$smarty->assign('form_tag', auto_gen_form('index.php?eps=post&amp;action='.$action.((($action == 'edit' || $action == 'delete') && $nid >= 1) ? '&amp;nid='.$nid : ''), 'postnews', true));

$smarty->assign('title', html_clean((isset($title)) ? $title : $news['title']));
$smarty->assign('content', html_clean((isset($content)) ? $content : $news['content']));
$smarty->assign('imgurl', html_clean((isset($imgurl)) ? $imgurl : $news['imgurl']));
$smarty->assign('type', (isset($type)) ? $type : $news['type']);
$smarty->assign('no_smiley', (isset($no_smiley)) ? $no_smiley : $news['no_smiley']);

unset($nid, $action, $news, $errors, $title, $content, $imgurl, $type, $no_smiley, $page_title, $req_fields);
$smarty->display('module/post.tpl');
?>
