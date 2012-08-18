/*
    File: eps_global.js
    Unit: GLOBAL JAVASCRIPT
  Author: Quoc Viet [aFeLiOn]
   Begin: 2006-04-09
*/

var eps_js_dir = 'js/';
var eps_loaded = new Object();

function eps_load(name)
{
	if (eps_loaded[name] != true)
	{
		document.write('<script type="text/javascript" src="' + eps_js_dir + name + '.js"></script>');
		eps_loaded[name] = true;
	}
}

// Body Onload
function addLoadEvent(func)
{
	var oldonload = window.onload;
	if (typeof(window.onload) != 'function')
	{
		window.onload = func;
	}
	else
	{
		window.onload = function()
		{
			oldonload();
			func();
		};
	}
}

/*
create_element(
	{
		tag: 'div',
		id: 'my_id',
		style: {display: 'block'},
		children:{
	        tag: 'span',
	        children:
			[
				{},
				{}
			]
	    }
	},
	'container_id'
);
*/
function create_element(data, append_obj)
{
	var el;
	if (typeof(data) == 'string')
        el = document.createTextNode(data);
    else
    {
		el = document.createElement(data.tag);
		delete(data.tag);
		
		//append the children
		if (typeof(data.children) != 'undefined')
		{
			if (typeof(data.children) == 'string' || typeof(data.children.length) == 'undefined')
			{
				//strings and single elements
				el.appendChild(create_element(data.children));
			}
			else
			{
				//arrays of elements
				for (var i=0, child = null; typeof(child = data.children[i]) != 'undefined'; i++)
				{
					el.appendChild(create_element(child));
				}
			}
			delete(data.children);
		}
	
		//any other data is attributes
		for (attr in data)
		{
			if (attr == 'style')
			{
				for (attr2 in data.style)
				{
					el.style[attr2] = data.style[attr2];
				}
			}
			else
				el.setAttribute(attr, data[attr]);
		}
	}
	if (typeof(append_obj) == 'undefined')
		return el;
	else
		append_obj.appendChild(el);
}

// Check form input
function chk_input(f)
{
	if (f.submit)
		f.submit.disabled = true;
	for (var i = 0; i < f.length; i++)
	{
		var el = f.elements[i];
		if (el.name && el.name.substring(0, 4)=='req_')
		{
			if (el.type && ((el.type == 'text' || el.type == 'textarea' || el.type == 'password' || el.type == 'file') && el.value === '') || (el.type == 'checkbox' && el.checked === false))
			{
				elname = el.name.substring(4, el.name.length);
				if (jslang[elname])
					alert('"' + jslang[elname] + '" ' + jslang['required']);
				else
					alert('"' + elname.replace('_', ' ').toUpperCase() + '" ' + 'is required');
				el.focus();
				if (f.submit)
					f.submit.disabled = false;
				return false;
			}
		}
	}
	return true;
}

function c_nav(na)
{
	$('eps_nav').className = 'nav in_' + na;
}

// Checkbox
function switchAll(frm, name)
{
	var chkBoxes = $(frm).elements[name];
	if (!chkBoxes.length)
		chkBoxes.checked = (chkBoxes.checked) ? false : true;
	else
	{
		for (var i = 0; i < chkBoxes.length; i++)
		{
			chkBoxes[i].checked = (chkBoxes[i].checked) ? false : true;
		}
	}
}

//----------------------------
var jslang = {};
eps_load('prototype');
eps_load('dhtmlHistory');
eps_load('eps_ajax');
eps_load('eps_bbcode');
