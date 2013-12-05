<?php

class WPxLogItemDisplay extends WPxItemDisplay {
	/**
	 * @var WPxLogItem
	 */
	protected static $default_classes = array('log-item');
	protected $item;
	protected $type = 'switch';

	// Note, if you call this directly, it doesn't run init...
	public function render_switch(){
		if ($this->item->is_expandable())
			$this->expandable();
		else {
			$this->standard();
		}
	}

	public function init(){
		if ($this->item->property()){
			$this->classes[] = "property";
		} else if ($this->item->index()){
			$this->classes[] = "index";
		}

		if (is_bool($this->item->value())){
			if ($this->item->value()){
				$this->classes[] = "bool";
				$this->classes[] = "true";
			} else {
				$this->classes[] = "bool";
				$this->classes[] = "false";
			}
		} else if (is_null($this->item->value())){
			$this->classes[] = "null";
		} else if (is_numeric($this->item->value())){
			$this->classes[] = "numeric";
		} else if (is_string($this->item->value())){
			$this->classes[] = "string";
		}

		if ($this->item->backtrace()){
			//m_debug($this->item->backtrace());
			global $wpx_log_level;
			$wpx_log_level++;
			$this->item->add(WPxLogger::Create($this->item->backtrace(), 'backtrace'));
			$wpx_log_level--;
			$this->type = 'hook';
		}
	}

	public function expandable(){
		?><div class="item expandable auto-init <?php $this->classes() ?>">
			<div class="preview"><?php $this->label(); ?></div>
			<div class="view children"><?php $this->children(); ?></div>
		</div><?php
	}

	public function children(){
		$this->item->children->render($this->get_passable_options());

		//foreach ($this->item->children() as $child){
		//	$previous_child = $child;
		//	if ($child->file() === $this->item->file()){
		//		// add child to this file container
		//	}
		//}
	}

	public function render_hook(){
		$bt = $this->item->backtrace()[3];
		$file = str_replace("\\", "/", $bt['file']);
		$file = str_replace($_SERVER['DOCUMENT_ROOT'], "/", $file);

		?><div class="context item expanded <?php $this->classes() ?>">
			<div class="preview file-name"><?= $file ?></div>
			<div class="line-preview">
				<div class="line-number"><?= $bt['line'] ?></div>
				<div class="function-name"><?= $bt['function'] ?>()</div>
			</div>
			<div class="view">
				<?php	$this->render_switch(); ?>
			</div>
		</div><?php
	}

	public function standard(){
		?><div class="item <?php $this->classes() ?>"><?php $this->label(); ?><?php $this->value()?></div><?php
	}

	public function value(){
		if (is_bool($this->item->value())){
			if ($this->item->value()){
				?><span class="item-value bool true">true</span><?php
			} else {
				?><span class="item-value bool false">false</span><?php
			}
		} else if (is_null($this->item->value())){
			?><span class="item-value null">null</span><?php
		} else if (is_numeric($this->item->value())){
			?><span class="item-value numeric"><?= $this->item->value() ?></span><?php
		} else if (is_string($this->item->value())){
			?><span class="item-value string"><span class="string-quote">"</span><?= $this->item->value() ?><span class="string-quote">"</span></span><?php
		}
		?><div class="clear"></div><?php
	}

	public function label(){
		if ($this->item->property()){
			$this->property_label();
		} else if ($this->item->index()){
			$this->index_label();
		} else {
			?><span class="item-label"><?php $this->var_name() ?></span><?php
		}
	}

	public function property_label(){
		?><span class="object-prop-name item-label"><span class="object-arrow">-></span><?php $this->var_name() ?></span><?php
	}

	public function index_label(){
		?><span class="array-index-name item-label"><?php
			?><span class="array-index-bracket">[</span><?php
				echo is_string($this->item->label()) ? '<span class="array-index-quote">"</span>' : null;
					$this->var_name();
				echo is_string($this->item->label()) ? '<span class="array-index-quote">"</span>' : null;
			?><span class="array-index-bracket">]</span><?php
		?></span><?php
	}

	public function var_name(){
		?><span class="var-name"><?=$this->item->label()?></span><?php
	}
}


/**
 * @property WPxValueLogItem $item
 */
class WPxValueLogItemDisplay extends WPxLogItemDisplay {

}


/**
 * @property WPxStringLogItem $item
 */
class WPxStringLogItemDisplay extends WPxValueLogItemDisplay {

}


/**
 * @property WPxObjectLogItem $item
 */
class WPxObjectLogItemDisplay extends WPxValueLogItemDisplay {

	// doesn't inherit... :(
	public $classes = array('object', 'value');

	public function label(){
		parent::label(); ?><span class="object-preview">(object <?=get_class($this->item->value())?>)</span><?php
	}

	public function view(){
		?><div class="view">
			<?php $this->item->children->render($this->get_passable_options()); ?>
		</div><?php
	}
}


/**
 * @property WPxArrayLogItem $item
 */
class WPxArrayLogItemDisplay extends WPxValueLogItemDisplay {

	// DONT USE THIS, omg! public function init(){}

	// doesn't inherit... :(
	public $classes = array('array', 'value');

	public function label(){
		parent::label(); ?><span class='array-preview'>(array <?= count($this->item->value())?>)</span><?php
	}

	public function view(){
		?><div class="view">
			<?php $this->item->children->render($this->get_passable_options()); ?>
		</div><?php
	}
}


/**
 * @property WPxHookLogItem $item
 */
class WPxHookLogItemDisplay extends WPxValueLogItemDisplay {
	// doesn't inherit... :(
	public $classes = array('hook', 'value');
	protected $type = "hook";


}

class WPxFileLogItem extends WPxItemDisplay {
	public function render_switch(){

	}
}