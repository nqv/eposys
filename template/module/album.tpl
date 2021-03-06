<div class="title"><h2>{$eps_lang.Page_album}</h2></div>
{if (empty($album))}
<div class="album">
	{foreach from=$album_shows item=cur_album}
		<span class="{cycle name='album' values='albumodd,albumeven'}">{$cur_album}</span>
		{cycle name='album_sperator' values=',<div class="clear" style="margin-top:10px"></div>'}
	{/foreach}
</div>

{elseif $album == 'user'}
<div class="gallery">
	<h3>{$album_name}</h3>
	{foreach from=$gallery_shows item=cur_gallery}
		<a href="index.php?mode=show&amp;eps=album&amp;album={$album|escape:'url'}&amp;pic={$cur_pic}" target="_black">
		<img src="{$album_dir}{$cur_thumb}" alt="" />
		</a>
	{/foreach}
	<br />
	<div>
	<form id="gallery" method="post" enctype="multipart/form-data" action="index.php?eps=album&amp;album=user&amp;p={$p}" onsubmit="return chk_input(this)">
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
</div>

{else}
<div class="gallery">
	{include file="jump.tpl"}
	<h3>{$album_name}</h3>
	{foreach from=$album_pics key=cur_thumb item=cur_pic}
		<a href="index.php?mode=show&amp;eps=album&amp;album={$album|escape:'url'}&amp;pic={$cur_pic}" target="_black">
		<img src="{$album_dir}{$cur_thumb}" alt="" />
		</a>
	{/foreach}
</div>
{/if}
