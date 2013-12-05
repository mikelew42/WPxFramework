<?php

class WPxWPBase extends WPxBase {

	protected $_wp; // WP_* object
	protected $_wp_props = array(); // list of properties on the WP_* object
	protected $_meta_keys = array();
	protected static $_wp_prop_aliases = array('id' => 'ID'); // 'alias' => 'prop', 'content' => 'post_content'
	protected static $_meta_key_aliases = array();
	protected $_modified_wp_fields = array();  // when a wp field gets modified, it gets added here

	/**
	 * Aliases are NOT case sensitive, due to strtolower.  If you
	 * make an alias for id=>ID, then Id and iD would work also.
	 */
	protected function _get_wp_prop_from_alias($name){
		if (array_key_exists(strtolower($name), static::$_wp_prop_aliases))
			return static::$_wp_prop_aliases[strtolower($name)];
		return false;
	}

	protected function _get_meta_key_from_alias($name){
		if (array_key_exists(strtolower($name), static::$_meta_key_aliases))
			return static::$_meta_key_aliases[strtolower($name)];
		return false;
	}

/**********************
 **  get() and set() **
 **********************/
	protected function _is_meta_key($name){
		return in_array($name, $this->_meta_keys);
	}

	protected function _is_wp_prop($name){
		return isset($this->_wp->{$name});
	}

	public function get($name){
		if (method_exists($this, $get_method = 'get_' . $name))
			return $this->{$get_method}();

		if ($this->_is_wp_prop($name))
			return $this->_wp_get($name);

		if ($wp_prop = $this->_get_wp_prop_from_alias($name))
			return $this->_wp_get($wp_prop);

		if ($this->_is_meta_key($name))
			return $this->get_meta($name);

		if ($meta_key = $this->_get_meta_key_from_alias($name))
			return $this->get_meta($meta_key);

		return $this->{$name};
	}

	public function set($name, $value){
		// $this->set_{$name}() ?
		if (method_exists($this, $set_method = 'set_' . $name)){
			$this->{$set_method}($value);

		// $this->_wp->{$name} ?
		} else if ($this->_is_wp_prop($name)) {
			$this->_wp_set($name, $value);

		// wp alias ?
		} else if ($wp_prop = $this->_get_wp_prop_from_alias($name)) {
			$this->_wp_set($wp_prop, $value);

		// meta key ?
		} else if ($this->_is_meta_key($name)) {
			$this->set_meta($name, $value);

		// meta alias ?
		} else if ($meta_key = $this->_get_meta_key_from_alias($name)) {
			$this->set_meta($meta_key, $value);

		// default
		} else {
			$this->{$name} = $value;
		}

		// maintain chainability
		return $this;
	}

	protected function _wp_get($name){
		if (!$this->_wp)
			$this->_wp = new stdClass();

		return $this->_wp->{$name};
	}

	protected function _wp_set($prop, $value){
		if ($this->_wp_get($prop) != $value){
			$this->_modified_wp_fields[] = $prop;
			$this->_wp->{$prop} = $value;
		}
		return $this;
	}

/* THESE SHOULD BE OVERRIDDEN WITH PROPER META METHODS */
	public function get_meta($key){ return $this; }

	public function set_meta($key, $value){ return $this; }

	public function add_meta($key, $value){ return $this; }

	public function delete_meta($key){ return $this; }

	public function get_meta_array($key){ return $this; }
}