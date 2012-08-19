/*
--------------------------------------------------------------------------------
     File:  class_ajax.js

    Class:  AJAX
   Author:  Quoc Viet [aFeLiOn]
    Email:  v@s.vnn.vn
    Begin:  2006-01-06
--------------------------------------------------------------------------------
*/

function vajax()
{
	this.xmlhttp = null;
	this.method = 'GET';
	this.statusid = 'ajax_status';	// Status ID
	this.id = null;	// Contain ID
	this.url = null;	// Url String
	this.frm = null;	// Form
	this.lang = new Array();
	this.lang['Loading'] = 'Đang tải...';
	this.lang['Error'] = 'Lỗi';
	this.lang['Not_get_element'] = 'Không tìm thấy đối tượng';
	this.lang['Empty_id'] = 'Đối tượng chưa được thiết lập';
	this.lang['No_xmlhttp'] = 'Trình duyệt của bạn không hỗ trợ Ajax';
	
	// Set Value
	this.set = function(u0, i0)
	{
		this.url = u0.replace('&amp;', '&');
		this.id = i0;
	}

	// document.getElementById()
	this.$ = function(a)
	{
		if (!a)
		{
			alert(this.lang['Empty_id']);
			return '';
		}
		if (typeof(a) == 'string')
		{
			if (document.getElementById(a))
				return document.getElementById(a);
			else
			{
				alert(this.lang['Not_get_element'] + ': ' + a);
				return;
			}
		}
		else
			return a;
	}

	this.status_disp = function(st)
	{
		document.body.style.cursor = 'wait';
		document.body.style.filter = 'gray()';
		window.status = st;
		this.$(this.statusid).innerHTML = st;
		this.$(this.statusid).style.display = 'block';
	}

	this.status_hide = function()
	{
		document.body.style.cursor = 'auto';
		document.body.style.filter = 'none';
		window.status = '';
		this.$(this.statusid).style.display = 'none';
	}

	// Read Form
	this.frmvalue = function()
	{
		if (!this.frm)
			return '';

		var frmvalues = new Array();
		var frmobj = this.$(this.frm);

		if (frmobj && frmobj.tagName == 'FORM')
		{
			var frmelem = frmobj.elements;
			for (var i = 0; i < frmelem.length; i++)
			{
				if (frmelem[i].type && (frmelem[i].type == 'radio' || frmelem[i].type == 'checkbox') && frmelem[i].checked == false)
					continue;
				if (frmelem[i].disabled && frmelem[i].disabled == true)
					continue;
				var name = frmelem[i].name;
				if (name)
				{
					if (frmelem[i].type == 'select-multiple')
					{
						for (var j = 0; j < frmelem[i].length; j++)
						{
							if (frmelem[i].options[j].selected == true)
								frmvalues.push(name + '=' + encodeURIComponent(frmelem[i].options[j].value));
						}
					}
					else
						frmvalues.push(name + '=' + encodeURIComponent(frmelem[i].value));
				}
			}
		}
		return frmvalues.join("&");
	}

	// Create XMLHttp
	this.create = function()
	{
		var req = null;
		try
		{
			req = new XMLHttpRequest();
			return req;
		}
		catch (e)
		{
			var msxml = new Array('MSXML2.XMLHTTP', 'Microsoft.XMLHTTP', 'MSXML2.XMLHTTP.5.0', 'MSXML2.XMLHTTP.4.0', 'MSXML2.XMLHTTP.3.0');
			for (var i = 0; i < msxml.length; i++)
			{
				try
				{
					req = new ActiveXObject(msxml[i]);
					return req;
				}
				catch (e1)
				{
				}
			}
			return null;
		}
	}

	// Send Request
	this.send = function(u1, i1)
	{
		this.xmlhttp = this.create();
		this.set(u1, i1);

		if (this.xmlhttp)
		{
			var self = this;

			this.xmlhttp.onreadystatechange = function()
			{
				switch (self.xmlhttp.readyState)
				{
					case 1:
					case 2:
					case 3:
						self.status_disp(self.lang['Loading']);
						break;
					case 4:
						if (self.xmlhttp.status == 200)
						{
							self.status_hide();
							var resp_content = self.xmlhttp.responseText;
							self.$(self.id).innerHTML = resp_content;
							var page_redir = /<meta http-equiv="refresh"/i;
							if (resp_content.match(page_redir))
							{
								var redir_url = /\; *?URL=(.+?)"/i;
								redir_match = redir_url.exec(resp_content);
								if (redir_match)
									window.setTimeout('location.href="' + redir_match[1] + '"', 800);
							}
						}
						break;
				}
			}
			this.xmlhttp.open(this.method, this.url, true);
			if (this.method == 'POST')
				this.xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
			this.xmlhttp.send(this.frmvalue());
		}
		else
		{
			alert(this.lang['No_xmlhttp']);
		}
	}

	// Send Form
	this.sendform = function(u2, i2, f2)
	{
		this.frm = f2;
		this.method = this.$(this.frm).method.toUpperCase();
		this.send(u2, i2);
	}
}
