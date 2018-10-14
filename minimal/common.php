<?php
/*=============================================================================
  Created by NxtChg (admin@nxtchg.com), 2018. License: Public Domain.
=============================================================================*/

function isset_or(&$var, $default = ''){ return (isset($var) ? $var : $default); }

function not_post()
{
	return (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] != 'POST');
}//____________________________________________________________________________

function arg($name, $type = 'string')
{
	$val = '';

	if(isset($_GET [$name])) $val = $_GET [$name]; else
	if(isset($_POST[$name])) $val = $_POST[$name];

	settype($val, $type);
	
	return $val;
}//____________________________________________________________________________

function cook($name, $type = 'string')
{
	$val = '';

	if(isset($_COOKIE[$name])) $val = $_COOKIE[$name];

	settype($val, $type);
	
	return $val;
}//____________________________________________________________________________

function random_str($len)
{
	$str = ''; $possible = "abcdefghijklmnopqrstuvwxyz0123456789"; $plen = strlen($possible);

	mt_srand();

	for($i = 0; $i < $len; $i++)
	{
		$rnd = (is_callable('random_int') ? random_int(0, $plen-1) : mt_rand(0, $plen-1));

		$str .= substr($possible, $rnd, 1);
	}

	return $str;
}//____________________________________________________________________________

function JsonError($code, $msg){ die(json_encode(['status' => $code, 'message' => $msg])); }
//_____________________________________________________________________________

function JsonSuccess(){ die(json_encode(['status' => 0, 'message' => ''])); }
//_____________________________________________________________________________

?>