<?php

/**
 * Simply an alias for $wpx_log->log();
 */
function wpx_log(){
	global $wpx_log;
	call_user_func_array(array($wpx_log, 'log'), func_get_args());
}

function wpx_gdv(array $gdv){
	global $wpx_log;
	$wpx_log->log_gdv($gdv);
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
	?><div class="wpx-toggle wpx-item wpx-log-item wpx-item-big wpx-collapsed">
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