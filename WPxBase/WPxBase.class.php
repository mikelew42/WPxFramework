<?php

class WPxBase {


	/**
	 * This IS case sensitive for normal get/set.  Can you set a value to null
	 * with this method?  I think array(null) will pass t/f.
	 */
	public function __call($name, $args){
		return $args ? $this->set($name, $args[0]) : $this->get($name);
	}

	public function __get($name){
		return $this->get($name);
	}

	public function __set($name, $value){
		return $this->set($name, $value);
	}

	public function set($name, $value){
		// does set_{$name}() method exist?  if so, use that
		if (method_exists($this, $set_method = 'set_' . $name))
			$this->{$set_method}($value);

		// if not, just set it
		else
			$this->{$name} = $value;

		// maintain chainability
		return $this;
	}

	public function get($name){
		return method_exists($this, $get_method = 'get_' . $name) ? $this->{$get_method}() : $this->{$name};
	}
}