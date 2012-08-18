{$js_lang}
<div class="title"><h2>{$eps_lang.Page_share}</h2></div>
{$error_show}
<div class="post">
	<form id="share_edit" method="post" enctype="multipart/form-data" action="index.php?eps=share&amp;action={$action}&amp;shid={$shid}" onsubmit="return chk_input(this)">
		<div class="inform">
			<fieldset>
				<legend>{$eps_lang.Legend_share}</legend>
				<div class="infieldset txtinput">
					<p>{include file='avim.tpl'}</p>
					<p><strong>{$eps_lang.File}: {$share_name}</strong></p>
					<p>{$eps_lang.Post_time}: {$share_post_time}</p>
					<label><strong>{$eps_lang.Comment}</strong><br /><input class="longinput" type="text" name="req_comment" value="{$comment}" size="50" maxlength="99" /><br /></label>
					<p><input type="button" onclick="this.form.change_file.style.display='block'" value="{$eps_lang.Change_file}" /></p>
					<label><input type="file" name="change_file" size="50" maxlength="255" value="" style="display:none" /></label>
				</div>
			</fieldset>
			<input type="hidden" name="MAX_FILE_SIZE" value="{$eps_config.max_size_upload}">
			<input type="hidden" name="form_sent" value="1" />
			<input type="hidden" name="form_user_id" value="{$eps_user.id}" />
			<p><input type="submit" name="submit" value="{$eps_lang.$action}" /><a href="javascript:history.go(-1)">{$eps_lang.Go_back}</a></p>
		</div>
	</form>
</div>
