<div class="title"><h2>{$eps_lang.Page_album}</h2></div>
{$error_show}
<div class="gallery">
	<h3>{$album_name}</h3>
	{foreach from=$gallery_shows item=cur_gallery}
		{$cur_gallery}
	{/foreach}
</div>
<div class="pagination">{$pagination}</div>
<br />
<div>
<form id="gallery" method="post" enctype="multipart/form-data" action="index.php?eps=gallery" onsubmit="return chk_input(this)">
	<div class="inform">
		<fieldset>
			<legend>{$eps_lang.Legend_upload}</legend>
			<div class="infieldset txtinput">
				<p>{include file='avim.tpl'}</p>
				<label><strong>{$eps_lang.File}</strong><br /><input type="file" name="req_file" size="50" maxlength="255" /><br /></label>
				<label><strong>{$eps_lang.Description}</strong><br /><input class="longinput" type="text" name="req_description" value="{$description}" size="50" maxlength="255" /><br /></label>
			</div>
		</fieldset>
		<input type="hidden" name="MAX_FILE_SIZE" value="{$eps_config.max_size_upload}">
		<input type="hidden" name="form_sent" value="gallery" />
		<input type="hidden" name="form_user_id" value="{$eps_user.id}" />
		<p><input type="submit" name="submit" value="{$eps_lang.Submit}" /><a href="javascript:history.go(-1)">{$eps_lang.Go back}</a></p>
	</div>
</form>
</div>
