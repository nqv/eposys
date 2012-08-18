{$js_lang}
<div class="title"><h2>{$eps_lang.Page_profile}</h2></div>
{$error_show}
<div>
	{$form_tag1}
		<div class="inform">
			<fieldset>
				<legend>{$eps_lang.Legend_essential}</legend>
				<div class="infieldset txtinput">
{if $is_admin}
					<label><strong>{$eps_lang.Username}</strong><br /><input type="text" name="req_username" size="30" maxlength="25" value="{$username}" /><br /></label>
{else}
					<p><strong>{$eps_lang.Username}: </strong>{$user.username}</p>							
{/if}
					<p><strong>{$eps_lang.Register_date}: </strong>{$user.reg_time}</p>
					<p>{$changepass_link}</p>
					<label><strong>{$eps_lang.Email}</strong><br /><input type="text" name="req_email" size="30" maxlength="70" value="{$email}" /><br /></label>
					<label><strong>{$eps_lang.StudentID}</strong><br /><input type="text" name="req_s_id" size="30" maxlength="8" value="{$s_id}" /><br /></label>
{if count($groups)}
					<label><strong>{$eps_lang.Group}</strong><br />
						<select name="group_id">
							{html_options options=$groups selected=$user.group_id}
						</select><br />
					</label>
					<label class="inline"><strong>{$eps_lang.Active}</strong><br />
						{html_radios name="active" options=$yesno_radios selected=$user.active}
						<br />
					</label>
{else}
					<p><strong>{$eps_lang.Group}: </strong>{$user.g_title}</p>
{/if}
					<label><strong>{$eps_lang.Style}</strong><br />
						<select name="style">
							{html_options values=$styles selected=$user.style output=$styles}
						</select><br />
					</label>
					<label><strong>{$eps_lang.Language}</strong><br />
						<select name="language">
							{html_options values=$languages selected=$user.language output=$languages}
						</select><br />
					</label>
					<label><input type="checkbox" name="use_ajax" value="1"{if $use_ajax} checked="checked"{/if} />{$eps_lang.Use_ajax} {$eps_lang.Ajax_comment}<br /></label>
					<input type="hidden" name="form_sent" value="1" />
					<input type="hidden" name="profile_id" value="{$user.id}" />
					<p><input type="submit" name="submit" value="{$eps_lang.Submit}" /><a href="javascript:history.go(-1)">{$eps_lang.Go_back}</a></p>
				</div>
			</fieldset>
		</div>
	</form>
</div><br />
<div>
	{$form_tag2}
		<div class="inform">
			<fieldset>
				<legend>{$eps_lang.Legend_another}</legend>
				<div class="infieldset txtinput">
					<p>{include file='avim.tpl'}</p>
					<p><strong>{$eps_lang.Name}: </strong>{$user.name}</p>
					<p><strong>{$eps_lang.Birthday}: </strong>{$user.birth}</p>
					<p><strong>{$eps_lang.Course}: </strong>{$user.course}</p>
					<label><strong>{$eps_lang.Native}</strong><br /><input type="text" name="native" class="longinput" size="50" maxlength="150" value="{$native}" /><br /></label>
					<label><strong>{$eps_lang.Address}</strong><br /><input type="text" name="address" class="longinput" size="50" maxlength="150" value="{$address}" /><br /></label>
					<label><strong>{$eps_lang.Phone}</strong><br /><input type="text" name="phone" size="30" maxlength="70" value="{$phone}" /><br /></label>
					<label><strong>{$eps_lang.Yahoo}</strong><br /><input type="text" name="yahoo" size="30" maxlength="70" value="{$yahoo}" /><br /></label>

					<input type="hidden" name="form_sent" value="2" />
					<input type="hidden" name="profile_id" value="{$user.id}" />
					<input type="hidden" name="eps_s_id" value="{$user.s_id}" />
					<p><input type="submit" name="submit" value="{$eps_lang.Submit}" /><a href="javascript:history.go(-1)">{$eps_lang.Go_back}</a></p>
				</div>
			</fieldset>
		</div>
	</form>
</div>
