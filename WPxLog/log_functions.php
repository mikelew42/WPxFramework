<?php

/**
 * Simply an alias for $wpx_log->log();
 */
function wpx_log(){
	global $wpx_log;
	return call_user_func_array(array($wpx_log, 'log'), func_get_args());
}

function wpx_dump($value, $label = "Unnamed"){
	$logger = new WPxLogger('wpx_dump');
	$logger->log($value, $label);
	$logger->naked(array('dump' => true));
	return $logger;
}

function wpx_gdv(array $gdv){
	global $wpx_log;
	return $wpx_log->log_gdv($gdv);
}
function wpx_print($o, $label = null){
	// have some config here to default this call to collapsed items, or expanded items, or no grouping
	// then, you can use a combination of these functions, or not have to worry about using the
	// appropriate _c if you just want them all to be collapsed.

	global $wpx; // and use $wpx['collapse_all_print_statements'];
	// or
	global $wpx_collapse_all_print_statements;
	if ($wpx_collapse_all_print_statements)
		wpx_print_c($o, $label);
}

function wpx_print_c($o, $label = null){
	// <item.collapsed><pre> print_r </pre>
}

function wpx_debug($o, $label = null){
	if ($label === null)
		$label = 'Unnamed';
	if (is_object($o)){
		wpx_debug_object($o, $label);
	} else if (is_array($o)){
		wpx_debug_array($o, $label);
	} else {
		wpx_debug_item($o, $label);
	}
}

function wpx_group($name){
	global $wpx_log;
	return $wpx_log->log('Group', $name, true);
}

function wpx_end(){
	global $wpx_log;
	return $wpx_log->end();
}

function wpx_globals(){
	$logger = new WPxLogger();
	$globs = $GLOBALS;
	unset($globs['GLOBALS']);
	$logger->log($globs, '$GLOBALS');
	$logger->render(array('globals' => true));
	return $logger;
}

function wpx_debug_array($array, $label = 'wpx_debug_array'){
	wpx_item_toggle_start($label. ' (Array)');

	foreach ($array as $k => $v)
		wpx_debug($v, $k);

	wpx_item_toggle_end();
}

function wpx_debug_object($object, $label = 'wpx_debug_object'){
	wpx_item_toggle_start($label . ' (Object)');

	foreach (get_object_vars($object) as $k=>$v)
		wpx_debug($v, $k);

	wpx_item_toggle_end();
}
function wpx_item_toggle_start($title){
	?><div class="wpx-toggle wpx-item wpx-log-item wpx-item-big">
	<div class="wpx-item-preview wpx-title"><?= $title ?></div>
	<div class="wpx-item-content wpx-content"><?php
}
function wpx_item_toggle_end(){
	?></div></div><?php
}
function wpx_debug_item($value, $label = 'wpx_debug_item'){
	?><div class="wpx-item wpx-log-item wpx-item-big">
	<div class="wpx-title"><?= $label ?> (<?= gettype($value) ?>): <?= $value ?></div>
	</div><?php
}