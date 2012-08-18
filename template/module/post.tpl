{$js_lang}
<div class="title"><h2>{$page_title}</h2></div>
{$error_show}
<div>
	{$form_tag}
		<div class="inform">
			<fieldset>
				<legend>{$eps_lang.Legend_post_news}</legend>
				<div class="infieldset txtinput">
					<div class="toolbar">
	    				<p>{include file='avim.tpl'}</p>
	    				<p>{include file='js_bbcode.tpl'}</p>
	    			</div>
					<label><strong>{$eps_lang.Title}</strong><br /><input class="longinput" type="text" name="req_title" value="{$title}" size="50" maxlength="99" tabindex="{counter start=1}" /><br /></label>
					<label><strong>{$eps_lang.Content}</strong><br />
						<textarea id="post_content" name="req_content" rows="20" tabindex="{counter}">{$content}</textarea>
					</label>
					<script type="text/javascript">
					<!--
					js_bb.set('post_content');
					//-->
					</script>
				</div>
			</fieldset>
			<fieldset>
				<legend>{$eps_lang.Option}</legend>
				<div class="infieldset txtinput">
					<label><strong>{$eps_lang.Image_link}</strong><br /><input type="text" name="imgurl" class="longinput" maxlength="255" tabindex="{counter}" value="{$imgurl}" /><br /></label>
					<label><strong>{$eps_lang.News_type}</strong><br /><input type="radio" name="type" value="1" tabindex="{counter}"{if $type == 1} checked="checked"{/if} />{$eps_lang.Main_news}<input name="type" type="radio" value="2" style="margin-left:10px" tabindex="{counter}"{if $type == 2} checked="checked"{/if} />{$eps_lang.Brief_news}<br /></label>
					<label><input type="checkbox" name="no_smiley" value="1" tabindex="{counter}"{if $no_smiley} checked="checked"{/if} />{$eps_lang.No_smiley}<br /></label>
				</div>
			</fieldset>
			<input type="hidden" name="form_sent" value="1" />
			<input type="hidden" name="form_user" value="{$eps_user.username}" />
			<p><input type="submit" name="submit" value="{$eps_lang.$action}" tabindex="{counter}" /><a href="javascript:history.go(-1)">{$eps_lang.Go_back}</a></p>
		</div>
	</form>
</div>
