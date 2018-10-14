/*=============================================================================
  Created by NxtChg (admin@nxtchg.com), 2017-2018. License: Public Domain.
=============================================================================*/

function fetchit2(self, url, data, cb)
{
	cb = (cb || { });

	function cb_err(r){ if(cb.error)     cb.error.apply(self, [r]); }
	function cb_suc(r){ if(cb.success) cb.success.apply(self, [r]); }

	// parse the url

	var method = 'GET', adr = '', timeout = null, json = false;

	var r = url.split(' ');

	if(r.length < 2) adr = r[0];
	else
	{
		adr = r[1]; r = r[0].split('(');

		method = r[0].toUpperCase(); if(method == 'POST') timeout = 12;
		
		if(r.length > 1) timeout = parseInt(r[1]);
	}

	// ----

	var xhr = new XMLHttpRequest;

	if(data instanceof FormData)
	{
		method = 'POST'; if(timeout === null) timeout = 12;
	}
	else if(data)
	{
		data = JSON.stringify(data);

		if(method == 'POST') json = true; else
		if(method == 'GET' )
		{
			adr += (adr.indexOf('?') < 0 ? '?' : '&') + data; data = null;
		}
	}

	xhr.open(method, adr, true);

	xhr.onreadystatechange = function()
	{
		if(this.readyState !== 4) return;

		if(this.status === 200)
		{
			var r = this.responseText;

			try{ r = JSON.parse(r); } catch(e){ console.error(r); cb_err('bad response'); return; }

			if(r.status != 0){ cb_err(r.message); return; }

			cb_suc(r);
		}
		else
		{
			cb_err('network error' + (this.status > 0 ? ': '+this.status : ''));
		}
	};

	xhr.timeout = (timeout || 6) * 1000; // time in milliseconds

	if(json) xhr.setRequestHeader('Content-Type', 'application/json');

	xhr.send(data);
}
