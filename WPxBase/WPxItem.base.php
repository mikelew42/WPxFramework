<?php

/* Potentially, at some point in the future, extend WPxWPBase from WPxItem so that all items can have hierarchical functionality
Could you create a type of method that runs up the tree?  Sure, why not:
	You have a method ->_climb('method', $params) that does something similar to __call on the children
	_climb could check the parent, then its parent, and keep going until it finds the method defined.
	Although, with my system of not defining methods, it might be hard to 'catch' this climb.
*/
/**
 * @method WPxItem root() Get or Set
 * @method WPxItem parent() Get or Set
 * @property WPxCollection $children
 * @property WPxItemDisplay $display
 */
class WPxItem extends WPxBase {

	// use root() and parent() to filter these through get_ and set_ methods
	protected $root;
	protected $parent;
	// Don't use this property directly.  Just use ->children, so that it'll trigger get_children()
	protected $_children;
	protected $_display;
	protected $title = "Default WPxItem Title";

	// Automatically called when you access $wpx_item->children
	// This is the only method that should use ->_children.  All the rest use ->children.
	public function get_display(){
		// wait until we need it to load it
		// @TODO Let this ride the inheritance chain, and use the closest parent Display class
		// Not sure how to get the parent class though..
		// The _display_class property could be used in a limited way, only when you need this Display class lookup
		// (when you don't want to define a new display class)
		if (!isset($this->_display)){
			$class_check = get_class($this) . "Display";
			//echo "class_check: " . $class_check;
			if (class_exists($class_check)){
				$Constructor = $class_check;
				$this->_display = new $Constructor($this);
			} else {
				$this->_display = new WPxItemDisplay($this);
			}
		}

		return $this->_display;
	}

	// this needs to be here so that ->set('display', $value) wouldn't create a real ->display property
	public function set_display($display){
		$this->_display = $display;
		return $this;
	}

	public function render($options = null){
		$this->display->render($options);
		return $this;
	}

	// Automatically called when you access $wpx_item->children
	// This is the only method that should use ->_children.  All the rest use ->children.
	public function get_children(){
		// wait until we need it to load it
		if (!isset($this->_children))
			$this->_children = new WPxCollection();

		return $this->_children;
	}

	// this needs to be here so that ->set('children', $value) wouldn't create a real ->children property
	public function set_children($children){
		$this->_children = $children;
		return $this;
	}

	public function add($item){
		$item->parent($this);
		$this->children->add($item);
	}

	public function has_children(){
		return $this->children->has_items();
	}

	public function is_expandable(){
		return $this->has_children();
	}
}

class WPxItemDisplay extends WPxDisplay {

	protected static $default_classes = array('item');
	/**
	 * @var WPxItem
	 */
	protected $item;
	protected $type = "switch"; // whatever you put here, this render_{"value"}() method will be used.

	public function __construct(WPxItem $item = null){
		$this->_merge_classes();
		if ($item)
			$this->item = $item;
	}

	protected function _merge_classes(){
		$this->classes = array_merge(parent::$default_classes, static::$default_classes);
	}

	public function render_switch(){
		if ($this->item->is_expandable())
			$this->expandable();
		else
			$this->standard();
	}

	public function standard(){
		?><div class="item <?php $this->classes() ?>"><?= $this->item->title(); ?></div><?php
	}

	public function expandable(){
		// maybe this is easier to just rewrite, and consolidate it as necessary...
		//$this->start();
		?><div class="item expandable auto-init <?php $this->classes() ?>">
			<?php $this->preview(); ?>
			<?php $this->view(); ?>
		</div><?php
		//$this->end();
	}

	public function start(){
		?><div class="item <?php $this->classes() ?>"><?php
	}

	public function end(){
		?></div><?php
	}

	public function preview(){
		?><div class="preview"><?= $this->item->label(); ?></div><?php
	}

	public function view(){
		?><div class="view"><?php $this->children() ?></div><?php
	}

	public function children(){
		?><div class="children"><?php $this->item->children->render() ?></div><?php
	}
}