<?php
/*
--------------------------------------------------------------------------------
     File:  punbb_topic.php

   Module:  PUNBB TOPIC
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-02-10

  Comment:  Remember config
--------------------------------------------------------------------------------
*/
$forum_prefix = 'punbb_';

// Max topics displayed
$show_max_topics = 17;

// Length truncated
$max_subject_length = 40;

// Forum ID's
$forum_fid = '';

// Forum ID's to exclude?
$forum_nfid = '';

if (empty($forum_prefix))
	return;

$forum_sql = '';
// Was any specific forum ID's supplied?
if (!empty($forum_fid))
{
	$fids = explode(',', trim($forum_fid));
	$fids = array_map('intval', $fids);

	if (!empty($fids))
		$forum_sql = ' AND f.id IN('.implode(',', $fids).')';
}

// Any forum ID's to exclude?
if (!empty($forum_nfid))
{
	$nfids = explode(',', trim($forum_nfid));
	$nfids = array_map('intval', $nfids);

	if (!empty($nfids))
		$forum_sql = ' AND f.id NOT IN('.implode(',', $nfids).')';
}

$result = $epsclass->db->query('SELECT t.id, t.subject FROM '.$forum_prefix.'topics AS t INNER JOIN '.$forum_prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$forum_prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id=3) WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.moved_to IS NULL'.$forum_sql.' ORDER BY t.last_post DESC LIMIT '.$show_max_topics) or error('Unable to fetch topic list', __FILE__, __LINE__, $epsclass->db->error());

echo '<ul>';
while ($cur_topic = $epsclass->db->fetch_assoc($result))
{
	if (eps_strlen($cur_topic['subject']) > $max_subject_length)
		$cur_topic['subject'] = eps_truncate($cur_topic['subject'], $max_subject_length);

	echo '<li>'.gen_link('forum/viewtopic.php?id='.$cur_topic['id'].'&amp;action=new', htmlspecialchars($cur_topic['subject']), htmlspecialchars($cur_topic['subject'])).'</li>'."\n";
}
echo '</ul>';
