<div id="eps_left">
	<div id="eps_nav" class="nav">{$navlink}</div>
	{foreach from=$eps_left item=cur_left}
		{assign var="tpl_box" value=$cur_left}
		{include file="box.tpl"}
	{/foreach}
</div>
