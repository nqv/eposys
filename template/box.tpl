<div{if (isset($tpl_box.id))} id="{$tpl_box.id}"{/if} class="{if (isset($tpl_box.style))}{$tpl_box.style} {/if}box">
	<div class="box_head">{$tpl_box.head}</div>
	<div{if (isset($tpl_box.content_id))} id="{$tpl_box.content_id}"{/if} class="inbox">{$tpl_box.content}</div>
</div>
