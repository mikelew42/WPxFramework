<?php

class WPxDisplay extends WPxBase {
	protected $id;
	protected $classes;
	protected $attr;
	protected $tag = 'div';
	protected $content;

	protected function classes($arr = null){
		$class_arr = array();

		if (is_null($arr)){
			if ($this->classes)
				$class_arr = $this->classes;
		} else {
			$class_arr = $arr;
		}


		if ($class_arr)
			echo ' class="' . implode(' ', $class_arr) . '" ';
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

	protected function id(){
		if ($this->id)
			echo "id='{$this->id}'";
	}

	public function render(){
		$this->start();
		$this->content();
		$this->end();
	}


	protected function start(){
		?><<?=$this->tag?> <?php $this->id() ?> <?php $this->classes() ?> <?php $this->attr() ?>><?php
	}

	protected function content(){
		echo $this->content;
	}

	protected function end(){
		?></<?=$this->tag?>><?php
	}
}
