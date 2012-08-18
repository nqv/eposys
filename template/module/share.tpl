{$js_lang}
<div class="title"><h2>{$eps_lang.Share}</h2></div>
{if count($shares)}
<fieldset>
	<legend>{$eps_lang.Legend_share}</legend>
	<div class="infieldset list">
		<ul>
	{foreach from=$shares item=cur_share}
			<li><strong>{$cur_share.link}</strong> --- <span>{$cur_share.comment}</span></li>
			<div class="desc">
		{if !empty($cur_share.editlink)}<div class="desc_right">{$cur_share.editlink}</div>{/if}
				{$cur_share.poster} &raquo; {$cur_share.post_time}
			</div>
	{/foreach}
		</ul>
		<div class="pagination">{$pagination}</div>
	</div>
</fieldset>
{/if}
{$error_show}
<div>
	<form id="share" method="post" enctype="multipart/form-data" action="index.php?eps=share&amp;p={$p}" onsubmit="return chk_input(this)">
		<div class="inform">
			<fieldset>
				<legend>{$eps_lang.Legend_upload}</legend>
				<div class="infieldset txtinput">
					<p>{include file='avim.tpl'}</p>
					<label><strong>{$eps_lang.File}</strong><br /><input type="file" name="req_file" size="50" maxlength="255" /><br /></label>
					<label><strong>{$eps_lang.Description}</strong><br /><input class="longinput" type="text" name="req_comment" value="{$comment}" size="50" maxlength="99" /><br /></label>
					<div class="note">{$eps_lang.Allow_file_type}: {$eps_config.upload_allowed|replace:',':', '}</div>
				</div>
			</fieldset>
			<input type="hidden" name="MAX_FILE_SIZE" value="{$eps_config.max_size_upload}">
			<input type="hidden" name="form_sent" value="1" />
			<input type="hidden" name="form_user_id" value="{$eps_user.id}" />
			<p><input type="submit" name="submit" value="{$eps_lang.Submit}" /><a href="javascript:history.go(-1)">{$eps_lang.Go back}</a></p>
		</div>
	</form>
</div>
