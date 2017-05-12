<?php

ini_set('display_errors', true);

try {
	require_once 'app/init.php';
	$app = new App;
}
catch (Exception $e) {
	echo 'There is an error on the website';
	// echo 'There is an error on the website: ' + $e->getMessage();
}