<div>
	{$form_tag}
		<div class="inform txtinput">
			<div class="toolbar">
	    		<p>{include file='avim.tpl'}</p>
	    	</div>
			<label><strong>{$eps_lang.Poll}</strong><br />
				<textarea name="poll_content" rows="15">{$poll_content}</textarea>
			</label>
			<label><input type="checkbox" name="reset" value="1" />Poll Reset</label>
			<input type="hidden" name="form_sent" value="1" />
			<p><input type="submit" name="submit" value="{$eps_lang.Submit}" /></p>
		</div>
	</form>
</div>
