<?php


class WPxLog extends WPxBase {

	/**
	 * @var WPxItem[]
	 */
	protected $children = array();

	public function log(){
		$backtrace = debug_backtrace();

	}

	public function log_gdv(array $gdv){

	}

	public function display(){

	}
}

global $wpx_log;
$wpx_log = new WPxLog();