<?php
/*=============================================================================
  Created by NxtChg (admin@nxtchg.com), 2018. License: Public Domain.
=============================================================================*/

	include('common.php');
	
	$token = arg('token');

	$request = "cashid:demo.cashid.info/basic/auth.php?x=$token";
?>
<!DOCTYPE html>
<html>
<head>
	<title>CashID : Manual</title>
	<meta name="viewport" content="width=800">
	<meta charset="utf-8"><meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="keywords" content="cashid,bitcoin,cash,demo">
	<meta name="description" content="A simple demo of CashID basic authentication.">
	<style>
		html,body,div,span,p,pre,a,img,b,u,i,center,form,label{ margin:0; padding:0; border:0; font-size:100%; font:inherit; vertical-align:baseline; }
	
    	b{ font-weight:bold; }
		i{ font-style:italic; }

		img{ outline:0; border:none; }

		a, a:visited, a:focus, a:active, a:hover{ color: #18f; text-decoration:none; outline:0; background-color:transparent; -webkit-text-decoration-skip: objects; cursor:pointer; }
		a:hover{ color:#F00; }

		body{ color:#444; background:#beb; line-height:1.5em; font-size:16px; font-family:sans-serif; }

		h2{ color:#494; }

		hr{ height:1px; width:100%; background:rgba(0,0,0,0.25); border:none; clear:both; }

		.page{ width:70%; min-height:600px; background:#f9f9f9; border:1px solid #bbb; margin:auto; padding:24px; border-radius:8px; }

		.form{ width:500px; margin:auto; margin-top:50px; }

		input{ padding:6px; width:500px; font-size:16px; }
		button{ padding:6px 12px; font-size:16px; }

		#error{ color:red; }

	</style>
	<script src="fetchit2.js"></script>
</head>
<body>
<br><br>
<div class=page>
	<h2>Sign the request manually:</h2>
	
	<div class=form>
		<b>Request:</b>  <br><input id=req type=text name=request readonly style="color:#777; background:#eee; border:1px solid #888;" value="<?=$request?>"><br><br>
		<b>Address:</b>  <br><input id=adr type=text name=address><br><br>
		<b>Signature:</b><br><input id=sig type=text name=signature><br><br>
		<span id=error></span>
		
		<center><br><button onclick="send_request()">Send</button></center>
	</div>
</div>
<script>

$ = function(id){ return document.getElementById(id); };

function send_request()
{
	var data = { request: $('req').value, address: $('adr').value, signature: $('sig').value };

	if(data.request == '' || data.address == '' || data.signature == '') return;

	fetchit2(this, 'POST auth.php', data,
	{
		success: function(r)
		{
			if(r.status == 0)
			{
				set_cookie('token', "<?=$token?>", 7);

				window.location.href = ''; // success, let's reload the page
			}
		},
		error: function(err){ $('error').innerText = 'Error: '+err; }
	});

	return true;
}
</script>
</body>
</html>
