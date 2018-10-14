<?php
/*=============================================================================
  Created by NxtChg (admin@nxtchg.com), 2018. License: Public Domain.
=============================================================================*/

	// execute this script periodically to expire old tokens

	set_time_limit(0);

	$dir = "tokens/";

	$time = time();

	$d = opendir($dir); if($d === false) die("ERROR: Failed to open the tokens folder!\n");

	while(($f = readdir($d)) !== false)
	{
		$t = @file_get_contents("$dir$f"); if(!intval($t) || $t == 'OK') continue;

		$age = $time - intval($t);
		
		echo "\n - token $f age $age sec";

		if($age > 300){ echo " <- deleting..."; unlink($dir.$f); }
	}

	closedir($d);
?>