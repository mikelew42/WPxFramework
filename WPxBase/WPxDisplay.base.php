<?php

class WPxDisplay extends WPxBase {

	protected $id;

	protected static $default_classes = array('wpxdisplay-default');

	// this has to be public so you can ->classes[] = push
	public $classes = array();
	protected $attr;
	protected $content = array();

	/**
	 * @var array $options
	 */
	protected $options = array();

	/**
	 * @var string $type
	 */
	protected $type = 'default';

	/**
	 * @var boolean $pass_options
	 */
	protected $pass_options = true;

	public function render($options = null){
		$this->init_options($options);
		$this->init();
		$this->_render();
	}
	public function test_meth(){ echo "test meth"; }

	protected function init(){} // leave blank for overrides

	protected function init_options($options = null){
		if (is_array($options)){
			$this->options = $options;
			foreach ($options as $k=>$v)
				$this->{$k} = $v;
		}
	}

	protected function _render(){
		// called after init_options and init
		$method_check = "render_" . $this->type;
		if (method_exists($this, $method_check)){
			$this->{$method_check}();

			// @TODO Notice:  Render method doesn't exist
		} else {
			$this->render_default();
		}
	}

	/* Maybe change this to only implode $class_arr, so you have to write class="in your markup".
	This whole display system needs to be much less invasive, and the user should still write std html ?><output><?php  */
	protected function classes($arr = null){
		$class_arr = array();

		if (is_null($arr)){
			if ($this->classes)
				$class_arr = $this->classes;
		} else {
			$class_arr = $arr;
		}


		if ($class_arr)
			echo implode(' ', $class_arr);
	}

	public function add_class($class){
		$this->classes[] = $class;
	}

	protected function attr($attr = null){
		$attr_arr = array();

		if (is_null($attr)){
			if ($this->attr)
				$attr_arr = $this->attr;
		} else {
			$attr_arr = $attr;
		}

		if ($attr_arr && is_array($attr_arr))
			foreach ($attr_arr as $k=>$v)
				echo "{$k}='{$v}'";
	}

	public function render_default(){
		$this->start();
		$this->content();
		$this->end();
	}

	// this is useful if you might not have an id..
	protected function id(){
		if ($this->id)
			echo "id='{$this->id}'";
	}

	protected function start(){
		?><div <?$this->id()?> class="default-class <?php $this->classes() ?>" data-default-attr="my-value" <?php $this->attr() ?>><?php
	}

	protected function content(){
		echo implode('', $this->content);
	}

	protected function end(){
		?></div><?php
	}

	public function test(){
		?><div>DISPLAY TEST!!!</div><?php
	}

	public function get_passable_options(){
		if ($this->pass_options)
			return $this->options;
		else
			return array();
	}
}
