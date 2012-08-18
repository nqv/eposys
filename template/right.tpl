<div id="eps_right">
	{foreach from=$eps_right item=cur_right}
		{assign var="tpl_box" value=$cur_right}
		{include file="box.tpl"}
	{/foreach}
	<div class="box">
		<div class="inbox"><a href="xml/eps_news.xml" target="_blank"><img src="image/rss.gif" alt="Eposys RSS" /></a></div>
	</div>
</div>
