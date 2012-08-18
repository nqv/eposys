<div class="title"><h2>{$page_title}</h2></div>
{$error_show}
<div>
	{$form_tag}
		<div class="inform">
			<fieldset>
				<legend>Site</legend>
				<div class="infieldset txtinput">
					<label><strong>Base url</strong><br /><input type="text" name="req_base_url" size="50" maxlength="255" value="{$base_url}" /><br /></label>
					<label><strong>Site title</strong><br /><input type="text" name="req_title" size="50" maxlength="255" value="{$title}" /><br /></label>
					<label><strong>Site desc</strong><br /><input type="text" name="req_desc" size="50" maxlength="255" value="{$desc}" /><br /></label>
					<label><strong>Site copyright</strong><br /><input type="text" name="req_copyright" size="50" maxlength="255" value="{$copyright}" /><br /></label>
					<label><strong>Site info</strong><br /><input type="text" name="info" size="50" maxlength="255" value="{$info}" /><br /></label>
				</div>
			</fieldset>
			<fieldset>
				<legend>Config</legend>
				<div class="infieldset txtinput">
					<label><strong>Time format</strong><br /><input type="text" name="req_time_format" size="50" maxlength="255" value="{$time_format}" /><br /></label>
					<label><strong>Date format</strong><br /><input type="text" name="req_date_format" size="50" maxlength="255" value="{$date_format}" /><br /></label>
					<label><strong>Default lang</strong><br />
						<select name="default_lang">
							{html_options values=$languages selected=$default_lang output=$languages}
						</select><br />
					</label>
					<label><strong>Default style</strong><br />
						<select name="default_style">
							{html_options values=$styles selected=$default_style output=$styles}
						</select><br />
					</label>
					<label><strong>Default group</strong><br /><input type="text" name="req_default_group" size="50" maxlength="255" value="{$default_group}" /><br /></label>
					<label><strong>Default timezone</strong><br /><input type="text" name="req_default_timezone" size="50" maxlength="255" value="{$default_timezone}" /><br /></label>
					<label class="inline"><strong>Default ajax</strong>{html_radios name="default_ajax" options=$yesno_radios selected=$default_ajax}<br /></label>
					<label><strong>Upload allowed</strong><br /><input type="text" name="req_upload_allowed" size="50" maxlength="255" value="{$upload_allowed}" /><br /></label>
					<label><strong>Max size upload</strong><br /><input type="text" name="req_max_size_upload" size="50" maxlength="255" value="{$max_size_upload}" /><br /></label>
					<label><strong>Redirect delay</strong><br /><input type="text" name="req_redirect_delay" size="50" maxlength="255" value="{$redirect_delay}" /><br /></label>
					<label class="inline"><strong>Gzip</strong>{html_radios name="gzip" options=$yesno_radios selected=$gzip}<br /></label>
					<label class="inline"><strong>Show poll</strong>{html_radios name="show_poll" options=$yesno_radios selected=$show_poll}<br /></label>
					<label class="inline"><strong>Show mark</strong>{html_radios name="show_mark" options=$yesno_radios selected=$show_mark}<br /></label>
				</div>
			</fieldset>
			<fieldset>
				<legend>Email</legend>
				<div class="infieldset txtinput">
					<label><strong>Webmaster email</strong><br /><input type="text" name="req_webmaster_email" size="50" maxlength="255" value="{$webmaster_email}" /><br /></label>
					<label><strong>Smtp host</strong><br /><input type="text" name="smtp_host" size="50" maxlength="255" value="{$smtp_host}" /><br /></label>
					<label><strong>Smtp user</strong><br /><input type="text" name="smtp_user" size="50" maxlength="255" value="{$smtp_user}" /><br /></label>
					<label><strong>Smtp pass</strong><br /><input type="text" name="smtp_pass" size="50" maxlength="255" value="{$smtp_pass}" /><br /></label>
				</div>
			</fieldset>
			<fieldset>
				<legend>Register</legend>
				<div class="infieldset txtinput">
					<label class="inline"><strong>Reg allow</strong>{html_radios name="reg_allow" options=$yesno_radios selected=$reg_allow}<br /></label>
					<label><strong>Rule</strong><br />
						<textarea name="req_rule" rows="10">{$rule}</textarea>
					</label>
				</div>
			</fieldset>
			<fieldset>
				<legend>Maintenance</legend>
				<div class="infieldset txtinput">
					<label class="inline"><strong>Maintenance</strong>{html_radios name="maintenance" options=$yesno_radios selected=$maintenance}<br /></label>
					<label><strong>Maintenance_message</strong><br />
						<textarea name="req_maintenance_message" rows="5">{$maintenance_message}</textarea>
					</label>
				</div>
			</fieldset>
			<input type="hidden" name="form_sent" value="eps_config" />
			<p><input type="submit" name="submit" value="Submit" /><a href="javascript:history.go(-1)">Go back</a></p>
		</div>
	</form>
</div>
