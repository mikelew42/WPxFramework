<?php


class WPxCollection {

	protected $children = array();

	public function __call($name, $args){
		if ($this->children)
			foreach ($this->children as $child)
				if (method_exists($child, $name))
					call_user_func_array(array($child, $name), $args);
		return $this;
	}
}