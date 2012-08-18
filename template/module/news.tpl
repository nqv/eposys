<div class="title"><h2>{$eps_lang.Page_news}</h2></div>
{foreach from=$news_shows item=cur_news}
<div class="{cycle name='news' values='newsodd,newseven'}">
	<div class="text">
		<h3>{$cur_news.title}</h3>
	{if (!empty($cur_news.imgurl))}<div class="topicimg"><img src="{$cur_news.imgurl}" alt="" /></div>{/if}
	{$cur_news.content}
	</div>
	<div class="desc">
	{if (!empty($cur_news.action))}<span class="desc_right">{$cur_news.action}</span>{/if}
	{$cur_news.desc}
	</div>
</div>
{/foreach}
{if $nid < 1}<div class="pagination">{$pagination}</div>{/if}
