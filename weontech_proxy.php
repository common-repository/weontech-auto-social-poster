<?php
require_once "weontech_request.php";
// this file takes care of posting the response data to WeOnTech
extract($_GET);

if($f) {	
	if($f == "services")
		$data = wot_getServices($key, $p);
	else if($f == "spinner")
		$data = wot_checkSpinner($key, $s);
	else
		$data = wot_getReport($key, $p);
}
else
	$data = wot_getInfo($key);
	
echo $data;
?>