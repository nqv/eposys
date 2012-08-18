<div class="today_birth"> 
{if count($birth_list)}
	<ul>
	{foreach from=$birth_list item=cur_birth}
		<li>{$cur_birth}</li>
	{/foreach}
	</ul>
{else}
	<span style="text-align:center">{$eps_lang.No_birthday}</span>
{/if}
</div>
{if count($t_birth_list)}
<div style="margin-top:10px;text-align:center">-----</div>
<div class="tomor_birth">
	<ul>
	{foreach from=$t_birth_list item=cur_birth}
		<li>{$cur_birth}</li>
	{/foreach}
	</ul>
</div>
{/if}
