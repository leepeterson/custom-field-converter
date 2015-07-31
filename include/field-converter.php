<?php 
	
	// If this file is called directly, abort.
	if (!defined('WPINC')) {die;}
	
	class blunt_field_converter {
		
		private $id = 0;
		private $active = false;
		private $hook = '';
		private $priority = 20;
		private $post_types = array();
		private $fields = array();
		private $related_posts = array();
		private $name = '';
		
		public function __construct($id) {
			// build this converter
			$this->id = $id;
			// preload all post_meta into wp's cache to reduce queries
			$meta = get_post_meta($id);
			$this->name = get_the_title($id);
			$this->build_converter();
		} // end public function __construct
		
		private function build_related_posts() {
			$repeater = 'related_posts';
			$count = intval(get_post_meta($this->id, $repeater, true));
			if (!$count) {
				return;
			}
			for ($i=0; $i<$count; $i++) {
				$related_post = $this->build_related_post($repeater.'_'.$i.'_');
				if (count($related_post)) {
					$this->related_posts[] = $related_post;
				}
			}
		} // end private function build_related_posts
		
		private function build_related_post($row) {
			$related_post = array();
			$repeater = $row.'pointer';
			$count = intval(get_post_meta($this->id, $repeater, true));
			if (!count($repeater)) {
				return;
			}
			$related_post['pointer'] = array();
			for ($i=0; $i<$count; $i++) {
				$related_post['pointer'][] = $this->built_pointer_field($repeater.'_'.$i.'_');
			}
			$related_post['fields'] = $this->build_fields($row);
		} // end private function build_related_post
		
		private function built_pointer_field($row) {
			$field = array(
				'type' => get_post_meta($this->id, $row.'type', true),
				'name' => get_post_meta($this->id, $row.'name', true)
			);
		} // end private function built_pointer_field
		
		private function build_fields($row='') {
			$repeater = $row.'fields';
			$count = intval(get_post_meta($this->id, $repeater, true));
			if (!$count) {
				return;
			}
			for ($i=0; $i<$count; $i++) {
				$this->fields[] = $this->build_field($repeater.'_'.$i.'_');
			}
		} // end private function build_fields
		
		private function build_field($row) {
			$field = array();
			$type = $field['type'] = get_post_meta($this->id, $row.'type', true);
			$hierarchy = array();
			if ($type == 'serialized') {
				$hierarchy[] = array(
					'type' => $type,
					'name' => get_post_meta($this->id, $row.'name', true),
				);
			} else {
				// nested field
				$repeater = $row.'hierarchy';
				$count = intval(get_post_meta($this->id, $repeater, true));
				for ($i=0; $i<$count; $i++) {
					$hierarchy[] = array(
						'type' => get_post_meta($this->id, $repeater.'_'.$i.'_type', true),
						'name' => get_post_meta($this->id, $repeater.'_'.$i.'_name', true)
					);
				} // end for
			} // end if else
			$field['meta_key'] = get_post_meta($this->id, $row.'meta_key', true);
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
			$this->build_fields();
			$this->build_related_posts();
			if (!count($this->fields) && !count($this->related_posts)) {
				$this->active = false;
			}
		} // end private function build_converter
		
	} // end class blunt_field_converter
	
?>