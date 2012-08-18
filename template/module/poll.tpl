<div>
{if !$show_result || $is_admin}
	{$form_tag}
		<div class="inform">
			<div class="poll_ques">{$poll_ques}</div>
			{html_radios name="eps_poll" options=$poll_radios}
    		<input type="hidden" name="form_sent" value="poll" />
			<p><input type="submit" name="submit" value="{$eps_lang.Submit}" />{$show_result_link}</p>
		</div>
	</form>
{/if}
{if $show_result || $is_admin}
	{if !$is_admin}<div class="poll_ques">{$poll_ques}</div>{else}<hr />{/if}
		<ul>
	{foreach from=$vote_result item=cur_vote}
			<li>{$cur_vote.ans}: <strong>{$cur_vote.rate}</strong> ({$cur_vote.vote})</li>
			<p><img src="image/poll_bar.png" width="{if $cur_vote.px == 0}1{else}{$cur_vote.px}{/if}" height="9" /></p></br />
	{/foreach}
		<ul>
	<div>{$eps_lang.Total_poll}: {$num_poll}</div>
	{if $polled}<div style="text-align:center">{$eps_lang.You_polled}</div>{/if}
{/if}
</div>
