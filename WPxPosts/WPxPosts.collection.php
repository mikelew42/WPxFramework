<?php

/**
 * Class WPxPosts
 * @method WPxPosts title($value) Set
 */
class WPxPosts extends WPxCollection {
	public function __construct(array $posts = null){
		if ($posts)
			$this->add_posts($posts);

		$this->display = new WPxPostsDisplay();
	}

	public function add_posts(array $posts = null){
		$this->children = array_merge($this->children, $posts);
	}
}