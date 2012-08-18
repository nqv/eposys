{$js_lang}
<div class="title"><h2>{$eps_lang.Page_register}</h2></div>
{$error_show}
<div>
	{$form_tag}
		<div class="inform">
			<fieldset>
				<legend>{$eps_lang.Legend_username}</legend>
				<div class="infieldset txtinput">
					<label><strong>{$eps_lang.Username}</strong><br /><input type="text" name="req_username" size="30" maxlength="25" value="{$username}" tabindex="{counter start=1}" /><br /></label>
					<div class="note">{$eps_lang.Username_comment}</div>
				</div>
			</fieldset>
			<fieldset>
				<legend>{$eps_lang.Legend_essential}</legend>
				<div class="infieldset txtinput">
					<label class="floatinline"><strong>{$eps_lang.Password}</strong><br /><input type="password" name="req_password1" size="30" maxlength="20" tabindex="{counter}" /><br /></label>
					<label class="floatinline"><strong>{$eps_lang.Password_confirm}</strong><br /><input type="password" name="req_password2" size="30" maxlength="20" tabindex="{counter}" /><br /></label>
					<div class="clear note">{$eps_lang.Password_comment}</div><br />
					<label class="floatinline"><strong>{$eps_lang.Email}</strong><br /><input type="input" name="req_email1" size="30" maxlength="70" value="{$email1}" tabindex="{counter}" /><br /></label>
					<label class="floatinline"><strong>{$eps_lang.Email_confirm}</strong><br /><input type="input" name="req_email2" size="30" maxlength="70" value="{$email2}" tabindex="{counter}" /><br /></label>
					<div class="clear note">{$eps_lang.Email_comment}</div><br />
					<label class="floatinline"><strong>{$eps_lang.StudentID}</strong><br /><input type="input" name="req_s_id" size="30" maxlength="8" value="{$s_id}" tabindex="{counter}" /><br /></label>
					<div class="clear note">{$eps_lang.StudentID_comment}</div>
				</div>
			</fieldset>
			<fieldset>
				<legend>{$eps_lang.Legend_visual_confirm}</legend>
				<div class="infieldset txtinput">
					<label>
						<strong>{$eps_lang.Visual_confirm}</strong><br /><input type="text" name="req_confirmcode" size="30" maxlength="6" tabindex="{counter}" />
						{$visual}
						<br />
					</label>
					<div class="note">{$eps_lang.Visual_comment}</div>
				</div>
			</fieldset>
			<fieldset>
				<legend>{$eps_lang.Legend_rule}</legend>
				<div class="infieldset txtinput">
					<label><strong>{$eps_lang.Rule}</strong><br />
						<textarea name="eps_rule" rows="10" cols="50" readonly="readonly">{$rule}</textarea>
					</label>
					<label><input type="checkbox" name="req_agree" value="1" tabindex="{counter}"{if ($req_agree)} checked="checked"{/if} />{$eps_lang.Rule_agree}<br /></label>
				</div>
			</fieldset>
			<input type="hidden" name="form_sent" value="1" />
			<p><input type="submit" name="submit" value="{$eps_lang.Submit}" tabindex="{counter}" /><a href="javascript:history.go(-1)">{$eps_lang.Go_back}</a></p>
		</div>
	</form>
</div>
