<!DOCTYPE html>
<html>
<head>
	<title>CashID : Basic Authentication demo</title>
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

		.login{ width:500px; margin:auto; margin-top:30px; }

		.qr{ margin:auto; text-align:center; padding-top:20px; }
		.qr a{ display:inline-block; }
		.qr .brd{ display:inline-block; height:256px; padding:12px; border:2px solid rgba(0,0,0,0.15); background:#fff; }
		.qr a div{ margin-bottom:6px; }
	</style>
	<script src="fetchit2.js"></script>
	<script src="qr.js"></script>
</head>
<body>
<br><br>
<div class=page>
<h2>Basic authentication demo</h2>

<?php
	include('common.php');

	$t = cook('token'); $auth = false;

	if(preg_match('/[a-z0-9]{12}/', $t))
	{
		$ft = @file_get_contents("tokens/$t"); if($ft === 'OK') $auth = true; // check if already authenticated
	}

	if($auth)
	{
		echo "<br><br>You are logged in with token <b>$t</b>. <a href=\"\" onclick=\"set_cookie('token', '', -1)\">Logout</a>";
	}
	else
	{
		$t = random_str(12); @file_put_contents("tokens/$t", time()); // generate and store the nonce/token

		$uri = "cashid:demo.cashid.info/basic/auth.php?x=$t";

		echo '<div class=login>';
		echo 	'<h3>Scan this QR code with your Identity Manager:</h3>';
		echo 	'<div class=qr>';
		echo 		"<a href=\"$uri\"><div class=brd><div id=qr></div></div><br>$uri</a><br>";
		echo 	'</div>';
		echo 	"<br><br><hr><br>Or you can sign the request <a href=\"manual.php?token=$t\">manually</a>.";
		echo '</div>';
		echo '<script>';
		echo 	"var cashid_token = '$t';";
		echo 	"var qr = new QRCode(document.getElementById('qr'),{ text: \"$uri\", width: 256, height: 256, colorDark: '#000', colorLight: '#fff', correctLevel: QRCode.CorrectLevel['L']});";
		echo '</script>';
	}
?>
<script>

function set_cookie(name, val, days, path) // (name, "", -1) to delete a cookie
{
	var enc = encodeURIComponent, v = enc(val);
	
	if(days)
	{
		var d = new Date(); d.setDate(d.getDate() + days);

		v += '; expires=' + d.toUTCString();
	}

	if(path) v += '; path=' + path;

	document.cookie = enc(name) + '=' + v; // + '; Secure'
}//____________________________________________________________________________

var token_checking = false;

function tick()
{
	if(token_checking) return;
	
	token_checking = true;

	fetchit2(this, 'check.php?token='+cashid_token, null,
	{
		success: function(r)
		{
			token_checking = false;

			if(r.status == 0)
			{
				set_cookie('token', cashid_token, 7);

				window.location.href = ''; // success, let's reload the page
			}
		},
		error: function(err){ token_checking = false; }
	});
}//____________________________________________________________________________

if(typeof(cashid_token) != 'undefined') setInterval(tick, 3000);

</script>
</div>
</body>
</html>