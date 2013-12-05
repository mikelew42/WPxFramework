<?php

add_action('init', 'wpx_enqueue');

function wpx_enqueue(){
	if (true){
		//wp_deregister_script('jquery');
		//wp_register_script('jquery', ("//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"), false, '1.10.2');
		//wp_enqueue_script('jquery');

		wp_deregister_script('jquery-ui');
		wp_register_script('jquery-ui', ("//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"), array('jquery'), '1.10.3');
		wp_enqueue_script('jquery-ui');
		wp_enqueue_style('wpx-google-fonts', "http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700");
	}

	if (!is_admin()){
	//wp_enqueue_style('wpx-debug', WPX_PLUGIN_URL . 'css/debug.css');
	//wp_enqueue_script('wpx-debug', WPX_PLUGIN_URL . 'js/debug.js', array('jquery', 'backbone'));
	}
}