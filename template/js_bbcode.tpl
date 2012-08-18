<div style="padding-top: 4px">
	<input type="button" value="B" name="B" style="font-weight:bold" onclick="js_bb.add('[b]','[/b]')" /> 
	<input type="button" value="I" name="I" style="font-style:italic" onclick="js_bb.add('[i]','[/i]')" />
	<input type="button" value="U" name="U" style="text-decoration:underline" onclick="js_bb.add('[u]','[/u]')" />
	<input type="button" value="S" name="S" style="text-decoration:line-through" onclick="js_bb.add('[s]','[/s]')" />
	<input type="button" value="url" name="url" onclick="js_bb.add('[url]','[/url]')" />
	<input type="button" value="img" name="img" onclick="js_bb.add('[img]','[/img]')" />
	<input type="button" value="email" name="email" onclick="js_bb.add('[email]','[/email]')" />
	<input type="button" value="code" name="code" onclick="js_bb.add('[code]','[/code]')" />
	<input type="button" value="quote" name="quote" onclick="js_bb.add('[quote]','[/quote]')" />
	<input type="button" value="left" name="left" onclick="js_bb.add('[left]','[/left]')" />
	<input type="button" value="center" name="center" onclick="js_bb.add('[center]','[/center]')" />
	<input type="button" value="right" name="right" onclick="js_bb.add('[right]','[/right]')" />
	<input type="button" value="size" name="size" onclick="js_bb.add('[size=2]','[/size]')" />
	<input type="button" value="red" name="red" style="color:#CC0000" onclick="js_bb.add('[color=#CC0000]','[/color]')" />
	<input type="button" value="green" name="green" style="color:#006600" onclick="js_bb.add('[color=#006600]','[/color]')" />
	<input type="button" value="blue" name="green" style="color:#3366CC" onclick="js_bb.add('[color=#3366CC]','[/color]')" />
	{if !empty($emoticons)}
	<input type="button" value=":)" name="emoticon" onclick="vShow('bb_emoticon');vFocus('bb_emoticon')" />
	<a href="javascript:;" id="bb_emoticon" class="dropdown" onblur="vHide(this)" onclick="vHide(this)">
		{foreach from=$emoticons item=cur_img key=cur_txt}
		<img src="image/emoticon/{$cur_img}" alt="{$cur_txt|escape:'html'}" onclick="js_bb.add('{$cur_txt|escape:'html'}','')" />
		{/foreach}
	</a>
	{/if}
</div>
