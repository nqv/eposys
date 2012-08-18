<?php
/*
--------------------------------------------------------------------------------
     File:  poll.php

   Module:  POLL
   Author:  Quoc Viet [aFeLiOn]
    Begin:  2006-03-04

  Comment:
--------------------------------------------------------------------------------
*/

if (!defined('IN_EPS'))
	exit;

$show = (!empty($_GET['result']) && $_GET['result']='poll') ? true : false;

$max_pixel = 100;
$poll_ques = '';
$poll_ans = array();
$poll_radios = array();
$num_poll = 0;

if (is_file(FILE_POLL_DATA))
{
	$polls = file(FILE_POLL_DATA);
	if (!empty($polls))
	{
		$poll_ques = html_clean(trim($polls[0]));
		for ($cur_id = 0; $cur_id + 1 < count($polls); $cur_id++)
		{
			$cur_line = trim($polls[$cur_id + 1]);
			if (empty($cur_line))
				continue;
			
			if (strpos($cur_line, '|') !== false)
			{
				$tmp = explode('|', $cur_line);
				$cur_ans = html_clean(trim($tmp[0]));
				$cur_vote = intval(trim($tmp[1]));
			}
			else
			{
				$cur_ans = html_clean($cur_line);
				$cur_vote = 0;
			}
			
			$poll_ans[$cur_id] = array('ans' => $cur_ans, 'vote' => $cur_vote);
			$poll_radios[$cur_id] = $cur_ans;
			$num_poll += $cur_vote;
		}
	}
}

$polled_ids = (is_file(FILE_POLL_ID)) ? explode("\n", file_get_contents(FILE_POLL_ID)) : array();
$polled_ips = (is_file(FILE_POLL_IP)) ? explode("\n", file_get_contents(FILE_POLL_IP)) : array();


if (empty($poll_ques) || empty($poll_ans))
	return;

$show_result = ($show || (!$eps_user['is_guest'] && in_array($eps_user['id'], $polled_ids)) || in_array($eps_user['ip_address'], $polled_ips)) ? true : false;

if ($show_result || IS_ADMIN)
{
	foreach ($poll_ans as $k => $v)
	{
		$rate = round($v['vote'] / $num_poll, 4);
		$poll_ans[$k]['rate'] = ($rate * 100).'%';
		$poll_ans[$k]['px'] = floor($rate * $max_pixel);
	}
	$smarty->assign('num_poll', $num_poll);
	$smarty->assign('vote_result', $poll_ans);
	$smarty->assign('polled', !$show);
}

if (!$show_result || IS_ADMIN)
{
	if (isset($_POST['form_sent']) && $_POST['form_sent'] == 'poll')
	{
		if (isset($_POST['eps_poll']))
		{
			$poll_ans[$_POST['eps_poll']]['vote']++;
			$poll_content = $poll_ques."\n";
			foreach ($poll_ans as $v)
			{
				$poll_content .= $v['ans'].' | '.$v['vote']."\n";
			}
			
			$polled_ips[] = $eps_user['ip_address'];
			if (!$eps_user['is_guest'])
				$polled_ids[] = $eps_user['id'];

			create_file($poll_content, FILE_POLL_DATA, true);
			create_file(implode("\n", $polled_ids), FILE_POLL_ID, true);
			create_file(implode("\n", $polled_ips), FILE_POLL_IP, true);

			redirect('index.php'.((isset($_GET['eps'])) ? '?eps='.$_GET['eps'] : ''), $eps_lang['Redirect_poll']);
		}
	}

	$smarty->assign('form_tag', auto_gen_form('index.php?eps=poll', 'poll', true));
	$smarty->assign('poll_radios', $poll_radios);
	$smarty->assign('show_result_link', auto_gen_link('index.php?eps=poll&amp;result=poll', $eps_lang['Show_result'], '', true));
}

$smarty->assign('show_result', $show_result);
$smarty->assign('is_admin', IS_ADMIN);
$smarty->assign('poll_ques', $poll_ques);

unset($show, $max_pixel, $poll_ques, $poll_ans, $poll_radios, $num_poll, $polled_ips, $polled_ids, $poll_content);
$smarty->display('module/poll.tpl');
?>
