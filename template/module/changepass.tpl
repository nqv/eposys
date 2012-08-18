{$js_lang}
<div class="title"><h2>{$eps_lang.Page_password_change}</h2></div>
{$error_show}
<div>
	{$form_tag}
		<div class="inform">
			<fieldset>
				<legend>{$eps_lang.Legend_change_password}</legend>
				<div class="infieldset txtinput">
{if $need_old_pass}
					<label><strong>{$eps_lang.Old_password}</strong><br /><input type="password" name="req_old_password" size="30" maxlength="25" /><br /></label><br />
{/if}
					<label><strong>{$eps_lang.New_password}</strong><br /><input type="password" name="req_new_password1" size="30" maxlength="25" /><br /></label>
					<label><strong>{$eps_lang.New_password_confirm}</strong><br /><input type="password" name="req_new_password2" size="30" maxlength="25" /><br /></label>
					<div>{$eps_lang.Password_comment}</div>
				</div>
			</fieldset>
			<input type="hidden" name="form_sent" value="1" />
			<input type="hidden" name="profile_id" value="{$uid}" />
			<p><input type="submit" name="submit" value="{$eps_lang.Submit}" /><a href="javascript:history.go(-1)">{$eps_lang.Go_back}</a></p>
		</div>
	</form>
</div>
