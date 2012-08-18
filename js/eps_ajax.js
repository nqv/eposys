/*
    File: eps_ajax.js
    Unit: CREATE AJAX REQUEST 
  Author: Quoc Viet [aFeLiOn]
   Begin: 2006-04-09
*/

var vajax =
{
	id_main: 'eps_main_content',
	indicator: '<div class="ajax_indicator">Đang tải...</div>',

	// run onload (this object must be 'vajax')
	initialize: function()
	{
	    dhtmlHistory.initialize();
    	dhtmlHistory.addListener(vajax.history.change);
	    if (dhtmlHistory.isFirstLoad())
		{
			dhtmlHistory.add('eps', $(vajax.id_main).innerHTML);
		}
	},

	// History
	history:
	{
		hash: '',

		create: function(u, i)
		{
			this.hash = (i == vajax.id_main) ? u : '';
		},
	
		change: function(newLocation, historyData)
		{
			if (historyData)
			{
				Element.update(vajax.id_main, historyData);
			}
		}
	},

	// Normalize URL
	normalize_url: function(u)
	{
		if (u.indexOf('index.php') >= 0)
			u = u.replace('index.php', 'module.php');
	 	else if (u.indexOf('?') == 0)
	 		u = 'module.php' + u;
		else
			u = 'module.php?' + u;
		return u;
	},

	update: function(u, i)
	{
		new Ajax.Updater(i, u, {method: 'GET', evalScripts: true});
	},
	
	form_update: function(u, f, i)
	{
		new Ajax.Updater(i, u, {method: $(f).method.toUpperCase(), parameters: Form.serialize(f), evalScripts: true});
	}
}

// Url, ID
function vQ(vu, vi, do_normalize)
{
	if (!vi)
		vi = vajax.id_main;

	vajax.history.create(vu, vi);

	if (!do_normalize || do_normalize == true)
		vu = vajax.normalize_url(vu);

	new Insertion.Bottom(vi, vajax.indicator);
	vajax.update(vu, vi);
}

// Url, Form, ID
function vF(vu, vf, vi, do_normalize)
{
	if (chk_input(vf))
	{
		if (!vi)
			vi = vajax.id_main;

		if (!do_normalize || do_normalize == true)
			vu = vajax.normalize_url(vu);

		new Insertion.Bottom(vi, vajax.indicator); 
		vajax.form_update(vu, vf, vi);
	}
	else
		return false;
}

addLoadEvent(vajax.initialize);
Ajax.Responders.register(
	{
		onComplete: function()
		{
			if (vajax.history.hash != '')
			{
				dhtmlHistory.add(vajax.history.hash, $(vajax.id_main).innerHTML);
			}
		}
	}
);
