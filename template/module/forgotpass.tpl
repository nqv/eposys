{$js_lang}
<div class="title"><h2>{$eps_lang.Page_password_forgot}</h2></div>
{$error_show}
<div>
	{$form_tag}
		<div class="inform">
			<fieldset>
				<legend>{$eps_lang.Legend_forgot_password}</legend>
				<div class="infieldset txtinput">
					<label><strong>{$eps_lang.Email}</strong><br /><input type="input" name="req_email" size="30" maxlength="60" /><br /></label>
					<label>
						<strong>{$eps_lang.Visual_confirm}</strong><br /><input type="text" name="req_confirmcode" size="30" maxlength="6" />
						{$visual}
					</label>
					<input type="hidden" name="form_sent" value="1" />
					<p><input type="submit" name="submit" value="{$eps_lang.Submit}" /><a href="javascript:history.go(-1)">{$eps_lang.Go_back}</a></p>
				</div>
			</fieldset>
		</div>
	</form>
</div>
