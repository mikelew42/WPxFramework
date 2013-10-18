<?php

class WPxPostDisplay extends WPxDisplay {

	/**
	 * @var WPxPost
	 */
	protected $post;

	public function __construct(WPxPost $post = null){
		if ($post)
			$this->post = $post;
	}

	public function render(){
		?><div class="post">
			<?php $this->title(); ?>
			<?php $this->content(); ?>
		</div><?php
	}

	public function title(){
		?><h2 class="post-title"><a href="<?= $this->post->permalink() ?>"><?= $this->post->title(); ?></a></h2><?php
	}

	public function content(){
		?><div class="post-content"><?= $this->post->content(); ?></div><?php
	}
}