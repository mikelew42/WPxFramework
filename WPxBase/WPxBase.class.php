<?php

/*
 * @property should be used for objects that need to be autoloaded.  Otherwise, you'll have to go through extra work to set up private _props.
 *
 * DO NOT RELY ON __GET AND __SET FOR PROTECTED PROPERTIES!!!
 * Internally, the property will be accessed directly, and externally it can't, so it'll trigger the __get/__set.
 * You don't want a different usage internally vs externally.
 * */

class WPxBase {


	/**
	 * This IS case sensitive for normal get/set.  Can you set a value to null
	 * with this method?  I think array(null) will pass t/f.
	 *
	 * Should this pass all the args for the setter?
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

	// This may not be entirely kosher.  This will autoload autoloaders.. so doing a if ($this->children) and isset($this->children) will always pass
	public function __isset($name){
		return !is_null($this->get($name));
	}

	public function set($name, $value){
		// this allows set_property() to be called without actually defining set_property(){}
		if (strpos($name, 'set_') === 0)
			$name = substr($name, 4);

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
		// this allows get_property() to be called without actually defining get_property(){}
		if (strpos($name, 'get_') === 0)
			$name = substr($name, 4);

		return method_exists($this, $get_method = 'get_' . $name) ? $this->{$get_method}() : $this->{$name};
	}
}