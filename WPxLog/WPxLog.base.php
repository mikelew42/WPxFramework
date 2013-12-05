<?php
/*
 * It might be nice to have a WPxItem as a hierarchical system that mimics jQuery.
 *
 * Do we need a WPxLog AND WPxLogItems???  Might not be a bad idea.  If there's a factory that will create the LogItems
 * externally, then I think it can all just be a LogItem.
 *
 * This is slightly COUNTER INTUITIVE:  WPxLog extends WPxLogItem
 * 		Add additional functionality to the root log item...
 * */
$wpx_log_level = 0;

/**
 * @method string label() Get or Set
 * @method WPxLogItem set_label() Get or Set
 * @method WPxLogger logger() Get or Set
 * @method array backtrace() Get or Set
 * @method WPxLogItem set_backtrace() Get or Set
 * @method boolean show_file() Get or Set
 * @method WPxLogItem set_show_file() Get or Set
 * @method boolean show_line() Get or Set
 * @method WPxLogItem set_show_line() Get or Set
 * @property WPxLogItemDisplay $display
 */
class WPxLogItem extends WPxItem {

	protected $label;
	protected $value;
	protected $logger;
	protected $backtrace;

	protected $file;
	protected $function;
	protected $line;

	protected $show_file;
	protected $show_line;
	protected $show_bt = false;

	protected $table = false;

	protected $title = "Default WPxLogItem Title";

	protected $table_row_name;
	protected $property;  // boolean, does this item belong to an object?
	protected $index;  //boolean, does this item belong to an array?

	public function init(){
		if ($this->backtrace()){
			$bt = $this->backtrace()[0];
			$this->file($bt['file'])->function($bt['function'])->line($bt['line']);
		}

		// very important
		return $this;
	}
	/**
	 * @param WPxLogger $logger
	 * @return WPxLogger
	 */
	public function set_logger(WPxLogger $logger){
		$this->root = $this->logger = $logger;
		return $this;
	}
}


class WPxValueLogItem extends WPxLogItem {

	protected $title = "Default WPxValueLogItem Title";

	public static function Create($value, $label, $backtrace = null, $logger = null){
		$new_value = new WPxValueLogItem();
		if ($logger) $new_value->logger($logger);
		if ($backtrace) $new_value->backtrace($backtrace);
		return $new_value->label($label)->value($value)->init();
	}
}

class WPxStringLogItem extends WPxValueLogItem {

	protected $title = "Default WPxStringLogItem Title";

	public static function Create($value, $label, $backtrace = null, $logger = null){
		$new_string = new WPxStringLogItem();
		if ($logger) $new_string->logger($logger);
		if ($backtrace) $new_string->backtrace($backtrace);
		return $new_string->label($label)->value($value)->init();
	}
}

class WPxObjectLogItem extends WPxValueLogItem {

	protected $object;  // just an alias for $value
	protected $title = "Default WPxObjectLogItem Title";

	public function set_object($object){
		global $wpx_log_level;
		$this->value = $this->object = $object;

		if ($wpx_log_level < 5){
			$wpx_log_level++;
			//echo "LOG LEVEL: $wpx_log_level <br />";
			$obj_arr = (array) $this->object;
			foreach ($obj_arr as $k=>$v){
				$new_property = WPxLogger::Create($v, $k);
				$new_property->property(true);
				$this->add($new_property);
			}
			$wpx_log_level--;
		} else {
			$this->add(WPxLogger::Create('LOG LIMIT REACHED', 'BLEH'));
		}
	}

	public static function Create($object, $label, $backtrace = null, $logger = null){
		$new_object = new WPxObjectLogItem();

		// set ->object() last, or at least after ->logger ?
		if ($logger) $new_object->logger($logger);
		if ($backtrace) $new_object->backtrace($backtrace);

		return $new_object->table(true)->label($label)->object($object)->init();
	}
}

class WPxArrayLogItem extends WPxValueLogItem {

	protected $array; // just an alias for $value

	protected $title = "Default WPxArrayLogItem Title";

	public function set_array($array){
		global $wpx_log_level;
		$this->value = $this->array = $array;

		if ($wpx_log_level < 5){
			$wpx_log_level++;
			//echo "LOG LEVEL: $wpx_log_level <br />";
			foreach ($this->array as $k => $v){
				$new_index = WPxLogger::Create($v, $k);
				$new_index->index(true);
				$this->add($new_index);
			}
			$wpx_log_level--;
		} else {
			$this->add(WPxLogger::Create('LOG LIMIT REACHED', 'BLEH'));
		}
	}

	public static function Create($array, $label, $backtrace = null, $logger = null){
		$new_array = new WPxArrayLogItem();

		// set ->object() last, or at least after ->logger ?
		if ($logger) $new_array->logger($logger);
		if ($backtrace) $new_array->backtrace($backtrace);

		return $new_array->table(true)->label($label)->array($array)->init();
	}
}

class WPxHookLogItem extends WPxLogItem {
	public static function Create($hook_name, $label, $backtrace = null, $logger = null){
		$new_hook = new WPxHookLogItem();

		// this is just for log_all right now
		$backtrace = array_slice($backtrace, 6);

		// set ->object() last, or at least after ->logger ?
		if ($logger) $new_hook->logger($logger);
		if ($backtrace) $new_hook->backtrace($backtrace);

		//$new_hook->show_bt(true);

		return $new_hook->table(true)->label($label)->value("hook")->init();
	}
}


class WPxLogger extends WPxBase {

	protected $item;
	/**
	 * @var WPxItem
	 */
	protected $current_item;

	// global default if each item isn't set
	protected $show_files = true;
	protected $show_lines = true;

	public function __construct($label = "WPxLogger"){
		$this->item = $this->current_item = new WPxLogItem();
		$this->item->set_logger($this)->set_label($label);
	}

	public function log($value, $label = "Unnamed", $open = false){
		$new_item = WPxLogger::Create($value, $label, debug_backtrace(), $this);
		$this->add($new_item, $open);
		return $new_item;
	}

	public function add(WPxLogItem $item, $open = false){
		$this->current_item->add($item);
		if ($open) $this->current_item = $item;
		return $this;
	}

	public function end(){
		$ret = $this->current_item;
		$this->current_item = $this->current_item->parent();
		return $ret;
	}

	public static function Create($value, $label, $backtrace = null, $logger = null){
		if ($backtrace && array_key_exists(5, $backtrace) && ( $backtrace[5]['line'] == 398 || $backtrace[5]['line'] == 172 )){
			return WPxHookLogItem::Create($value, $label, $backtrace, $logger);
		}

		if (is_object($value)){
			return WPxObjectLogItem::Create($value, $label, $backtrace, $logger);
		} else if (is_array($value)){
			return WPxArrayLogItem::Create($value, $label, $backtrace, $logger);
		} else if (is_string($value)){
			return WPxStringLogItem::Create($value, $label, $backtrace, $logger);
		} else {
			return WPxValueLogItem::Create($value, $label, $backtrace, $logger);
		}
	}

	public function log_gdv(array $gdv){

	}

	public function render($options = null){
		?><div class="wpx-logger"><?php $this->item->render($options); ?></div><?php
	}
	public function naked($options = null){
		?><div class="wpx-logger"><?php $this->item->children->render($options); ?></div><?php
	}
}

global $wpx_log;
$wpx_log = new WPxLogger();