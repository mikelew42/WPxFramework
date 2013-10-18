<?php
/*
Plugin Name: WPxFramework
Description: WP, enhanced
Version: 0.1.0
Author: Michael Lewis
*/

// Includes
define( 'WPX_VERSION', '0.1.0');
define( 'WPX_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define( 'WPX_PATH', plugin_dir_path(__FILE__) );
define( 'WPX_BASENAME', plugin_basename( __FILE__ ) );

// Folder Paths
define( 'WPX_CORE_CLASS_PATH', 'WPxCore');
define( 'WPX_PAGES_PATH', 'WPxPages');
define( 'WPX_POSTS_PATH', 'WPxPosts');
define( 'WPX_MODS_PATH', 'WPxMods');

require_once('prepend.php');

class WPxFramework extends WPxBase {
	
	public function __construct(){
		add_action('init', array($this, 'init'));
	}
	
	// Init function will run on every page load
	public function init(){
		
	}
}

//WPxRewrite::Init();  // I think this has to run outside of any hooks

global $wpx;

$wpx = new WPxFramework();