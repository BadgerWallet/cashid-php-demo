<!DOCTYPE html>
<html lang='en'>
	<head>
		<title>CashID with Badger Wallet</title>
		<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
		<link rel="icon" type="image/png" href="favicon.png" />
		<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
		<link rel='stylesheet' href='css/brand.css'>
		<link rel='stylesheet' href='css/demo.css'>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
		<script src="/lib/qr.js"></script>
		<script src="/lib/cashid.js"></script>
		<script src="/lib/badger-wallet.js"></script>
	</head>
	<body>
		<section>
			<header>CashID with Badger Wallet</header>
			<small>CashID enables users to sign in to web pages using their Bitcoin Cash keys. We see this as an entirely new paradigm in identity management and an enabling technology in our goal to be your idenity vault and gateway to BCH dapps.</small>
			<br/>
			<small>To get started, <a href='https://badgerwallet.cash/#/install'>Download the latest Badger Wallet</a> and follow the <a href='https://github.com/BadgerWallet/badger#installation-of-developer-preview'>instructions</a> for installing it.</small>
			<ul class='examples'>
<?php
	// Include the CashID support library for PHP.
	require_once('lib/cashid.php');

	$requestList =
	[
		'login' =>
		[
			'title' => 'Login to a dapp',
			'description' => 'Login using a session id, or use your own custom action and data.',
			'action' => 'login',
			'data' => '15366-4133-6141-9638',
			'metadata' => []
		],
		'minimal' =>
		[
			'title' => 'Minimal authentication request',
			'description' => 'The smallest possible request only authenticates a user.',
			'action' => null,
			'data' => null,
			'metadata' => []
		]
	];

	foreach($requestList as $requestName => $requestData)
	{
		// Create the minimal request
		$requestURI = $cashid->create_request($requestData['action'], $requestData['data'], $requestData['metadata']);

		// Parse the request to example the parts.
		$requestParts = $cashid->parse_request($requestURI);

		echo "				<li class='example' data-request-uri='{$requestURI}' data-request-nonce='{$requestParts['parameters']['nonce']}'>";
		echo "					<div class='requestDescription'>";
		echo "						<header class='requestTitle'>{$requestData['title']}</header>";
		echo "						<small class='form-text requestExplanation'>{$requestData['description']}</small>";
		echo "					</div>";
		echo "					<ul class='requestParts'>";
		echo "						<li class='requestView'>";
		echo "							<header>Request Code</header>";
		echo "							<small class='form-text text-muted requestText'>{$requestURI}</small>";
		echo "							<span class='requestQR'></span>";
		echo "							<pre>123</pre>";
		echo "						</li>";
		echo "						<li class='responseJSON'>";
		echo "							<header>Response Object</header>";
		echo "							<pre></pre>";
		echo "						</li>";
		echo "						<li class='confirmationJSON'>";
		echo "							<header>Confirmation Object</header>";
		echo "							<pre></pre>";
		echo "						</li>";
		echo "						<li>
												<button
													class='badger-wallet'
													data-action='cashid'
													data-cashid-request='{$requestURI}'
												>
													Login with Badger
												</button>
											</li><br/>";
		echo "					</ul>";
		echo "				</li>";
	}
?>
			</ul>
		</section>
		<script>
			window.onload = function()
			{
				function handle_response(event)
				{
					let exampleNodes = document.getElementsByClassName('example');

					for(node in exampleNodes)
					{
						if(typeof exampleNodes[node] == 'object')
						{
							if(exampleNodes[node].getAttribute('data-request-nonce') == event.target.nonce)
							{
								exampleNodes[node].getElementsByClassName('responseJSON')[0].getElementsByTagName("pre")[0].innerHTML = JSON.stringify(JSON.parse(event.data), null, 2);
							}
						}
					}

					// Print the content of the event to the webpage.
					console.log("RESPONSE");
					console.log(event.target.nonce);
					console.log(event.data);
				}

				function handle_confirmation(event)
				{
					let exampleNodes = document.getElementsByClassName('example');

					for(node in exampleNodes)
					{
						if(typeof exampleNodes[node] == 'object')
						{
							if(exampleNodes[node].getAttribute('data-request-nonce') == event.target.nonce)
							{
								exampleNodes[node].getElementsByClassName('confirmationJSON')[0].getElementsByTagName("pre")[0].innerHTML = JSON.stringify(JSON.parse(event.data), null, 2);
							}
						}
					}

					// Print the content of the event to the webpage.
					console.log("CONFIRMATION");
					console.log(event);
					console.log(event.data);
				}

				let exampleNodes = document.getElementsByClassName('example');

				for(node in exampleNodes)
				{
					if(typeof exampleNodes[node] == 'object')
					{
						let requestNonce = exampleNodes[node].getAttribute('data-request-nonce');
						let requestURI = exampleNodes[node].getAttribute('data-request-uri');

						let requestQR = exampleNodes[node].getElementsByClassName('requestQR')[0];
						let requestJSONview = exampleNodes[node].getElementsByClassName('requestView')[0].getElementsByTagName("pre")[0];

						let requestObject = parseCashIDRequest(requestURI);

						requestJSONview.innerHTML = JSON.stringify(requestObject, null, 2);

						let codeParameters =
						{
							text: requestURI,
							width: 192,
							height: 192,
							colorDark: '#000',
							colorLight: '#fff',
							correctLevel: QRCode.CorrectLevel['L']
						};

						// FIXME: Don't throwaway the reference to the QR code... ?
						let qr = new QRCode(requestQR, codeParameters);

						// Check for SSE support.
						if(typeof(EventSource) !== "undefined")
						{
							// Open a connection to an event stream.
							let event_source = new EventSource('api/event.php?x=' + requestNonce);

							// Store the nonce on the event for easier management.
							event_source.nonce = requestNonce;

							// Set a function to handle stream generic messages.
							event_source.onmessage = function(event)
							{
								// Pass the event to the display_event function.
								display_event(event);
							}

							// Add an event listener for response events..
							event_source.addEventListener('response', handle_response, false);

							// Add an event listener for response events..
							event_source.addEventListener('confirmation', handle_confirmation, false);
						}
						else
						{
							// SSE is not supported.
							console.log("The browser does not support Server Sent Events.");
						}

					}
				}
			}
		</script>
	</body>
</html>
