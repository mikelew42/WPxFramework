<?php
/**
 * @method int id() Get ID
 * @method WPxPost title() Get or Set
 * @method WPxPost content() Get or Set
 * @method WPxPost date() Get or Set
 * @method WPxPost date_gmt() Get or Set
 * @method WPxPost excerpt() Get or Set
 * @method WPxPost status() Get or Set
 * @method WPxPost password() Get or Set
 * @method WPxPost name() Get or Set
 * @method WPxPost modified() Get or Set
 * @method WPxPost modified_gmt() Get or Set
 * @method WPxPost parent() Get or Set
 * @method WPxPost type() Get or Set
 * @property WPxPostDisplay $display These change the chainability
 * @property WPxUser $author
 */
class WPxPost extends WPxWPBase {

	/*
	 * It appears as though $obj->protected_prop will check against the __get/__set magic methods
	 * before throwing an error that we can't access a protected property.  I thought this was different...
	 *
	 * Still, internally, $this->author or $this->display will access these null properties rather than the
	 * get_display and get_author magic methods.
	 */
	protected $_display;
	protected $_author;

	protected static $_wp_prop_aliases = array(
		'id' => 'ID',
		'content' => 'post_content',
		// Why not set this to 'author_id' => 'post_author', then, and forget the get_author_id function...
		// Can you not have ->author-> and ->author(13) ??  SEE NOTE BELOW @ get_author_id()...
		// I still need a get_author function, for ->author-> to work, I think...
	//	'author' => 'post_author', // use get_author_id if you need just the id
		'author_id' => 'post_author',
		'date' => 'post_date',
		'date_gmt' => 'post_date_gmt',
		'title' => 'post_title',
		'excerpt' => 'post_excerpt',
		'status' => 'post_status',
		'password' => 'post_password',
		'name' => 'post_name',
		'modified' => 'post_modified',
		'modified_gmt' => 'post_modified_gmt',
		'parent' => 'post_parent',
		'type' => 'post_type'
	);

	public function __construct($post_or_id = null){
		wpx_log($post_or_id, 'WPxPost->__construct($post_or_id)');
		if ($post_or_id)
			$this->load($post_or_id);
	}

	public function load($post_or_id = null){
		if(!$post_or_id || ($post_or_id instanceof WP_Error)){
			return false;
		} else if ($post_or_id instanceof WP_Post){
			return $this->load_by_wp_post($post_or_id);
		} else if (is_numeric($post_or_id) && $post_or_id > 0){
			return $this->load_by_post_id($post_or_id);
		}
	}

	/**
	 * This method is accessed when you $post->display->...
	 */
	public function get_display(){
		// wait until we need it to load it
		if (!isset($this->_display))
			$this->_display = new WPxPostDisplay($this);

		return $this->_display;
	}

	public function set_display($display){
		$this->_display = $display;
		return $this;
	}

	/**
	 * This method is accessed when you $post->author->...
	 */
	public function get_author(){
		// wait until we need it to load it
		//if (!isset($this->_author))
			//$this->_author = new WPxUser($this);

		return $this->_author;
	}

	public function set_author($author){
		$this->_author = $author;
		return $this;
	}

	/**
	 * This was a normal get/set via $this->author($set), however I wanted the author property to be the WPxUser object,
	 * and didn't want an author_id() function getting in the way of the auto complete.  Typing $this->aut[TAB] should
	 * always auto complete to the author property, not author_id().  So, use get_author_id() instead.
	 *
	 * I'm not sure which way I like better...
	 *
	 * @return int WP_User id

	public function get_author_id(){
		return $this->_wp_get('post_author');
	}
	 */


	public function save(){
		if ($this->_modified_wp_fields){
			m_debug($this->_modified_wp_fields);
			$postarr = array();

			foreach ($this->_modified_wp_fields as $field)
				$postarr[$field] = $this->_wp_get($field);

			if (!$this->id()){
				$post_id = wp_insert_post($postarr);
			} else {
				$postarr['ID'] = $this->id();
				$post_id = wp_update_post($postarr);
			}

			if ($post_id > 0){
				$this->load($post_id);
				$this->_modified_wp_fields = array();
			}
		}
		return $this;
	}

	protected function load_by_wp_post(WP_Post $post){
		$this->_wp = $post;
		return $this;
	}

	protected function load_by_post_id($post_id){
		return $post_id > 0 ? $this->load(get_post($post_id)) : false;
	}

	public function render($name = null){
		if (!$name)
			$this->display->render();

		else if (method_exists($this->display, $name))
			$this->display->{$name}();

		return $this;
	}

	public function get_post(){
		return $this->_wp;
	}

	public function get_permalink(){
		return get_permalink($this->id());
	}

	public function get_meta($key){
		return get_post_meta($this->id(), $key, true);
	}

	public function set_meta($key, $value){
		update_post_meta($this->id(), $key, $value);
		return $this;
	}

	public function add_meta($key, $value){
		add_post_meta($this->id(), $key, $value);
		return $this;
	}

	public function delete_meta($key){
		delete_post_meta($this->id(), $key);
		return $this;
	}

	public function get_meta_array($key){
		return get_post_meta($this->id(), $key);
	}

	public static function Create($post_or_id = null){
		return new WPxPost($post_or_id);
	}
}