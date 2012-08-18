<ul>
{foreach from=$marks item=cur_mark key=mark_name}
	<li>{$mark_name}: {$cur_mark}</li>
{/foreach}
</ul>
<div>{$eps_lang.Average}: {$average}</div>
