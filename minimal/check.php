<?php
/*=============================================================================
  Created by NxtChg (admin@nxtchg.com), 2018. License: Public Domain.
=============================================================================*/

	header('Content-type: application/json');

	include('common.php');

	$t = arg('token');

	if(preg_match('/[a-z0-9]{12}/', $t))
	{
		$ft = @file_get_contents("tokens/$t"); if($ft === 'OK') JsonSuccess();
	}

	JsonError(1, 'not logged in'); // this is not from the CashID spec - we just use the same format for our own error
?>