<div class="title"><h2>Extension</h2></div>
{$error_show}
<div>
	{$form_tag}
		<div class="inform">
			<fieldset>
				<legend>Archive</legend>
				<div class="infieldset txtinput">
					<label><strong>Unpack File</strong><br /><input type="text" name="req_unpack_file" size="50" maxlength="255" value="{$unpack_file}" /><br /></label>
					<label><strong>Unpack Destination</strong><br /><input type="text" name="req_unpack_dest" size="50" maxlength="255" value="{$unpack_dest}" /><br /></label>
					<input type="hidden" name="form_sent" value="archive" />
					<p><input type="submit" name="submit" value="Submit" /></p>
				</div>
			</fieldset>
		</div>
	</form>
</div>
<div>
	<form method="post" action="index.php?eps=extension" onsubmit="return chk_input(this)">
		<div class="inform">
			<fieldset>
				<legend>Mysql Backup</legend>
				<div class="infieldset txtinput">
					{html_radios name="task" options=$task_radios selected=$task}
					<input type="hidden" name="form_sent" value="backup" />
					<p><input type="submit" name="submit" value="Submit" /></p>
				</div>
			</fieldset>
			<p><a href="phpmyadminz">PhpMyAdmin</a> | <a href="{$bigdump_link}" target="_blank">Bigdump</a></p>
		</div>
	</form>
</div>
