{$js_lang}
{if $show_title}
<div class="title"><h2>{$eps_lang.Page_login}</h2></div>
{/if}
{$error_show}
<div>
	{$form_tag}
		<div class="inform txtinput">
			<label><strong>{$eps_lang.Username}</strong><br /><input type="text" name="req_username" size="{$size}" maxlength="25" value="{$username}" /><br /></label>
			<label><strong>{$eps_lang.Password}</strong><br /><input type="password" name="req_password" size="{$size}" maxlength="20" /><br /></label>
{if !$antiflood_allow}
				<label>
					<strong>{$eps_lang.Visual_confirm}</strong><br /><input type="text" name="req_confirmcode" size="{$size}" maxlength="6" />
					{$visual}
					<br />
				</label>
{/if}
			<label><input type="checkbox" name="auto" value="1"{if $auto} checked="checked"{/if} />{$eps_lang.Auto_login}<br /></label>
			<input type="hidden" name="form_sent" value="login" />
			<p><input type="submit" name="submit" value="{$eps_lang.Submit}" />{$forgotpass_link}</p>
		</div>
	</form>
</div>
