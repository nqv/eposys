<div class="title"><h2>{$eps_lang.Page_list}</h2></div>
<div class="list">
	{if ($list_type == 2)}{include file='jump.tpl'}{/if}
	<table class="tbl" cellspacing="0">
		<thead>
			<tr>
	{if ($list_type == 2)}
				<th class="coll" scope="col">{$eps_lang.Num}</th>
				<th class="col2" scope="col">{$eps_lang.Name}</th>
				<th class="col3" scope="col">{$eps_lang.Birthday}</th>
				<th class="colr" scope="col">{$eps_lang.Course}</th>
	{else}
				<th class="coll" scope="col">{$eps_lang.Num}</th>
				<th class="col2" scope="col">{$eps_lang.Username}</th>
				<th class="col3" scope="col">{$eps_lang.Register_date}</th>
				<th class="colr" scope="col">{$eps_lang.Group}</th>
	{/if}
			</tr>
		</thead>
		<tbody>
	{foreach from=$list_shows item=cur_user}
			<tr class="{cycle name='list' values='rowodd,roweven'}">
				<td class="coll">{$cur_user[0]}</td>
				<td class="col2">{$cur_user[1]}</td>
				<td class="col3">{$cur_user[2]}</td>
				<td class="colr">{$cur_user[3]}</td>
			</tr>
	{/foreach}
		</tbody>
	</table>
</div>
<div class="pagination">{$pagination}</div>
