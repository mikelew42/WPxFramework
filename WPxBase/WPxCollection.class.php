<?php

/* This could be integrated with WPxBase so that it first checks for WPxCollection->get_/set_ and then tries it on the children
UNTIL I FIND A BETTER SOLUTION, WPxCollection->property = "value" AND NOT WPxCollection->property("value");  The __call is overridden here
*/
/**
 * @property array $items
 */
class WPxCollection extends WPxBase {

	// Until I can merge this->__call with WPxBase->__call, then this class has to use public props
	public $items = array();

	public function __call($name, $args){
		if ($this->items)
			foreach ($this->items as $item)
				if (is_object($item) && method_exists($item, $name))
					call_user_func_array(array($item, $name), $args);
		return $this;
	}

	public function add($item){
		$this->items[] = $item;
	}

	/**
	 * @return boolean
	 */
	public function has_items(){
		return (boolean) count($this->items);
	}
}