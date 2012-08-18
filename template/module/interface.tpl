<div>
	<form id="interface" method="post" action="module.php?eps=interface">
		<div class="inform">
			<label><strong>{$eps_lang.Style}</strong><br />
				<select name="guest_style">
					{html_options values=$styles selected=$cur_guest_style output=$styles}
				</select><br />
			</label>
			<label><strong>{$eps_lang.Language}</strong><br />
				<select name="guest_language">
					{html_options values=$languages selected=$cur_guest_language output=$languages}
				</select><br />
			</label>
			<label><input type="checkbox" name="guest_ajax" value="1"{if ($cur_guest_ajax)} checked="checked"{/if} />{$eps_lang.Use_ajax}<br /></label>
			<input type="hidden" name="form_sent" value="interface" />
			<input type="hidden" name="redirect_to" value="{$redirect_to}" />
			<p><input type="submit" name="submit" value="{$eps_lang.Submit}" /></p>
		</div>
	</form>
</div>
