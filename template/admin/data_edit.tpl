<div>
	{$form_tag}
		<div class="inform txtinput">
			<div class="toolbar">
	    		<p>{include file='avim.tpl'}</p>
	    	</div>
	    	{if !empty($data_edit_comment)}<p>{$data_edit_comment}</p>{/if}
			<label><strong>{$eps_lang.Content}</strong><br />
				<textarea name="content" rows="15">{$content}</textarea>
			</label>
			<input type="hidden" name="form_sent" value="data_edit" />
			<p><input type="submit" name="submit" value="{$eps_lang.Submit}" /></p>
		</div>
	</form>
</div>
