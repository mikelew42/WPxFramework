<?php

//add_action('wp', 'wpx_tests');

function wpx_tests(){
	echo "TEST: <br />";

	$myar = array(1 => 1,2=>  2, 'three' => 3);
	$myar[0] = 'zero';

	wpx_debug($myar);
}
