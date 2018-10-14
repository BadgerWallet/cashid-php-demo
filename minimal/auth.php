<?php
/*=============================================================================
  Created by NxtChg (admin@nxtchg.com), 2018. License: Public Domain.
=============================================================================*/

	header('Content-type: application/json');

	include('common.php');
	
	if(not_post()) JsonError(2, 'need POST request');

	$data = json_decode(@file_get_contents("php://input"), true); if($data === NULL) JsonError(2, 'malformed request');

	$uri = isset_or($data['request']);
	$adr = isset_or($data['address']);
	$sig = isset_or($data['signature']);

	if($uri == '' || $adr == '' || $sig == '') JsonError(2, 'malformed request');

	if(preg_match('/x=([a-z0-9]{12})/', $uri, $match)) $token = $match[1]; else JsonError(2, 'nonce is missing');

	$f = @file_get_contents("tokens/$token"); if($f === false) JsonError(7, 'the nonce has expired');

	if($f == 'OK') JsonError(8, 'the nonce has already been used');

	$age = time() - intval($f); if($age > 300) JsonError(7, 'the nonce has expired');

	include('json_api.php');

	$api = new JsonAPI();

	$r = $api->verify_message($adr, $sig, $uri); if($r !== true) JsonError(9, 'signature verification failed');

	if(@file_put_contents("tokens/$token", 'OK') === false) JsonError(15, 'try again later please');

	JsonSuccess();
?>