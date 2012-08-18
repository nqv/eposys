{$js_lang}
<div class="title"><h2>{$eps_lang.Page_mark}</h2></div>
{$error_show}
<div class="mark">
	{include file='jump.tpl'}
	{$form_tag}
		<div class="inform">
			<table class="tbl" cellspacing="0">
				<thead>
					<tr>
						<th class="coll" scope="col">{$eps_lang.Num}</th>
						<th scope="col">{$eps_lang.Name}</th>
						{foreach from=$subjects item=cur_value key=cur_key}
						<th scope="col"><input type="checkbox" name="c_{$cur_key}[]" value="1" onclick="this.form.submit.disabled=false;this.checked=(this.checked)?false:true;switchAll(this.form, this.name)" />{$cur_key}</th>
						{/foreach}
					</tr>
				</thead>
				<tbody>
			{foreach from=$students item=cur_student name=student}
					<tr class="{cycle name='student' values='rowodd,roweven'}">
						<td class="coll">{$smarty.foreach.student.iteration}</td>
						<td>{$cur_student.name}</td>
						{foreach from=$subjects item=cur_value key=cur_key name=subject}
						<td align="center"><input name="{$cur_key}[{$cur_student.id}]" value="{$cur_student.$cur_key}" size="2" maxlength="2" tabindex="{$smarty.foreach.subject.iteration}" /></th>
						{/foreach}
					</tr>
			{/foreach}
				</tbody>
				<tfoot>
					<tr>
						<th class="coll" scope="col">&nbsp;</th>
						<th scope="col">&nbsp;</th>
						{foreach from=$subjects item=cur_value key=cur_key}
						<th scope="col"><input type="checkbox" name="c_{$cur_key}[]" value="1" onclick="this.form.submit.disabled=false;this.checked=(this.checked)?false:true;switchAll(this.form, this.name)" />{$cur_key}</th>
						{/foreach}
					</tr>
				</tfoot>
			</table>
			<input type="hidden" name="form_sent" value="mark" />
			<p><input type="submit" name="submit" value="{$eps_lang.Submit}" disabled="disabled" /><a href="javascript:history.go(-1)">{$eps_lang.Go_back}</a></p>
		</div>
	</form>
</div>
