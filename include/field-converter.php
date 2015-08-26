<?php 
	
	// If this file is called directly, abort.
	if (!defined('WPINC')) {die;}
	
	class blunt_field_converter {
		
		private $name = '';
		private $id = 0;
		private $active = false;
		private $hook = '';
		private $priority = 20;
		private $post_types = array();
		private $fields = array();
		private $related_posts = array();
		
		public function __construct($id) {
			// build this converter
			$this->id = $id;
			// preload all post_meta into wp's cache to reduce queries
			$meta = get_post_meta($id);
			$this->name = get_the_title($id);
			$this->build_converter();
		} // end public function __construct
		
		private function build_related_posts() {
			$related_posts = array();
			$repeater = 'related_posts';
			$count = intval(get_post_meta($this->id, $repeater, true));
			if (!$count) {
				return;
			}
			for ($i=0; $i<$count; $i++) {
				$related_post = $this->build_related_post($repeater.'_'.$i.'_');
				if (count($related_post)) {
					$related_posts[] = $related_post;
				}
			}
			return $related_posts;
		} // end private function build_related_posts
		
		private function build_related_post($row) {
			$related_post = array();
			$repeater = $row.'pointer';
			//echo $repeater,'<br>';
			$count = intval(get_post_meta($this->id, $repeater, true));
			if (!count($repeater)) {
				return;
			}
			$related_post['pointer'] = array();
			//echo ($count),'<br>';
			for ($i=0; $i<$count; $i++) {
				$related_post['pointer'][] = $this->build_pointer_field($repeater.'_'.$i.'_');
			}
			$related_post['site'] = false;
			if (is_multisite()) {
				$related_post['site'] = intval(get_post_meta($this->id, $row.'site', true));
			}
			$related_post['fields'] = $this->build_fields($row);
			return $related_post;
		} // end private function build_related_post
		
		private function build_pointer_field($row) {
			//echo $row,'<br>';
			$field = array(
				'type' => get_post_meta($this->id, $row.'type', true),
				'name' => get_post_meta($this->id, $row.'name', true)
			);
			//print_r($field); echo '<br>';
			return $field;
		} // end private function build_pointer_field
		
		private function build_fields($row='') {
			$fields = array();
			$repeater = $row.'fields';
			$count = intval(get_post_meta($this->id, $repeater, true));
			for ($i=0; $i<$count; $i++) {
				$fields[] = $this->build_field($repeater.'_'.$i.'_');
			}
			return $fields;
		} // end private function build_fields
		
		private function build_field($row) {
			$field = array();
			$type = $field['type'] = get_post_meta($this->id, $row.'type', true);
			$hierarchy = array();
			//echo $type, '<br>';
			if ($type == 'serialized') {
				$hierarchy[] = array(
					'type' => $type,
					'name' => get_post_meta($this->id, $row.'name', true),
				);
			} else {
				// nested field
				//echo 'here <br>';
				$repeater = $row.'hierarchy';
				//echo $repeater,'<br>';
				$count = intval(get_post_meta($this->id, $repeater, true));
				//echo $count,'<br>';
				for ($i=0; $i<$count; $i++) {
					$hierarchy[] = array(
						'type' => get_post_meta($this->id, $repeater.'_'.$i.'_type', true),
						'name' => get_post_meta($this->id, $repeater.'_'.$i.'_name', true)
					);
				} // end for
			} // end if else
			$field['hierarchy'] = $hierarchy;
			$field['meta_key'] = get_post_meta($this->id, $row.'meta_key', true);
			$empty = get_post_meta($this->id, $row.'empty', true);
			if (!$empty) {
				$field['empty'] = false;
			} else {
				$field['empty'] = true;
			}
			$field['default'] = get_post_meta($this->id, $row.'default', true);
			return $field;
		} // end private function build_field
		
		private function build_converter() {
			$active = get_post_meta($this->id, 'active', true);
			if ($active === '' || $active == 1) {
				$this->active = true;
			} else {
				// not active, skip rest of init
				return;
			}
			$post_types = get_post_meta($this->id, 'post_types', true);
			if ($post_types === '') {
				$this->active = false;
				return;
			}
			$post_types = maybe_unserialize($post_types);
			if (!is_array($post_types)) {
				$post_types = array($post_types);
			}
			$this->post_types = $post_types;
			$this->hook = get_post_meta($this->id, 'hook', true);
			if ($this->hook === '') {
				$this->active = false;
				return;
			}
			$priority = get_post_meta($this->id, 'priority', true);
			if ($priority === '') {
				$this->priority = 20;
			} else {
				$this->priority = intval($priority);
			}
			$this->fields = $this->build_fields();
			$this->related_posts = $this->build_related_posts();
			if (!count($this->fields) && !count($this->related_posts)) {
				$this->active = false;
			}
		} // end private function build_converter
		
	} // end class blunt_field_converter
	
?>