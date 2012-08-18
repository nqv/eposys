/*
    File: eps_bbcode.js
  Object: JS BBCODE
  Author: Quoc Viet [aFeLiOn] (modified form Punbb)
   Begin: 2006-02-16
*/

var js_bbcode =
{
	fieldid: null,

	set: function(fid)
	{
		this.fieldid = fid;
	},

	add: function(open, close)
	{
		try
		{
			field = document.getElementById(this.fieldid);
		}
		catch (e)
		{
			return;
		}
		// IE support
		if (document.selection && document.selection.createRange)
		{
			field.focus();
			sel = document.selection.createRange();
			sel.text = open + sel.text + close;
			field.focus();
		}
		// Moz support
		else if (field.selectionStart || field.selectionStart == '0')
		{
			var startPos = field.selectionStart;
			var endPos = field.selectionEnd;

			field.value = field.value.substring(0, startPos) + open + field.value.substring(startPos, endPos) + close + field.value.substring(endPos, field.value.length);
			field.selectionStart = field.selectionEnd = endPos + open.length + close.length;
			field.focus();
		}
		// Other browsers
		else
		{
			field.value += open + close;
			field.focus();
		}
		return;
	}
}