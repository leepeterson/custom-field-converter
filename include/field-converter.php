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
		
		private $clear = array(); // fields to clear
		private $data = array(); // data to be inserted for post
		
		private $reparing = false;
		private $last_repair = 'never';
		private $nuking = false;
		private $last_nuke = 'never';
		
		public function __construct($id) {
			// build this converter
			$this->id = $id;
			// preload all post_meta into wp's cache to reduce queries
			$meta = get_post_meta($id);
			$this->name = get_the_title($id);
			$this->build_converter();
			$this->add_hook();
			add_action('acf/update_post', array($this, 'update_this_post'));
		} // end public function __construct
		
		public function update_this_post($post_id) {
			if ($post_id != $this->id) {
				return;
			}
			$reparing = 0;
			if ($this->reparing) {
				$reparing = 1;
			}
			update_post_meta($this->id, '_blunt_field_converter_repairing', $this->reparing);
			$nuking = 0;
			if ($this->nuking) {
				$nuking = 1;
			}
			update_post_meta($this->id, '_blunt_field_converter_nuking', $this->nuking);
		} // end public function update_this_post
		
		public function update_post($post_id) {
			$post_type = get_post_type($post_id);
			if (!in_array($post_type, $this->post_types)) {
				return;
			}
			//return;
			//echo 'update_post <pre>'; print_r($this); die;
			// do get post meta to force wp to cache
			// get data from fields
			$this->get_fields_data($post_id, $this->fields);
			// get data from related posts
			$this->get_related_data($post_id, $this->related_posts);
			
			
			//echo '<pre>'; print_r($this->clear); print_r($this->data); die;
			
			
			// clear all post meta from fields
			$this->clear_post_meta($post_id);
			// add new post meta
			$this->update_post_meta($post_id);
			//echo '<pre>'; print_r($this->related_posts);
			
		} // end public function update_post
		
		private function clear_post_meta($post_id) {
			if (!count($this->clear)) {
				return;
			}
			global $wpdb;
			$post_id = $wpdb->_escape($post_id);
			$this->clear = $wpdb->_escape($this->clear);
			$table = $wpdb->get_blog_prefix().'postmeta';
			foreach ($this->clear as $index => $value) {
				$this->clear[$index] = '"'.$value.'"';
			}
			$query = 'DELETE FROM '.$table.'
			 					WHERE post_id = "'.$post_id.'" 
							 		AND meta_key IN ('.implode(', ', $this->clear).')';
			//echo $query.'<br><br>';
			//echo $query,'<br /><br />';
			$success = $wpdb->query($query);
			/*
			if ($success === false) {
				die('delete failed');
			}
			echo $success.'<br>';
			*/
		} // end private function clear_post_meta
		
		private function update_post_meta($post_id) {
			if (!count($this->data)) {
				return;
			}
			global $wpdb;
			$post_id = $wpdb->_escape($post_id);
			$this->data = $wpdb->_escape($this->data);
			$table = $wpdb->get_blog_prefix().'postmeta';
			$values = array();
			$query = 'INSERT INTO '.$table.' (post_id, meta_key, meta_value) VALUES ';
			foreach ($this->data as $meta_key => $datas) {
				$meta_key = $wpdb->_escape($meta_key);
				foreach ($datas as $data) {
					$values[] = '("'.$post_id.'", "'.$meta_key.'", "'.$data.'")';
				}
			}
			$query .= implode(','."\r\n", $values);
			//echo $query,'<br /><br />';
			$success = $wpdb->query($query);
			/*
			if ($success === false) {
				die('insert failed');
			}
			echo $success.'<br>';
			*/
		} // end private function update_post_meta
		
		private function clear_additional_fields() {
			$fields = apply_filters('blunt_field_converter/clear_fields', array());
			if (!is_array($fields) && !count($fields)) {
				return;
			}
			foreach ($fields as $field) {
				if (!in_array($field, $this->clear)) {
					$this->clear[] = $field;
				}
			}
		} // end private function clear_additional_fields
		
		private function add_additional_data() {
			$fields = apply_filters('blunt_field_converter/additional_data', array());
			if (!is_array($fields) || !count($fields)) {
				return;
			}
			foreach ($fields as $index => $values) {
				if (!in_array($index, $this->clear)) {
					$this->clear[] = $index;
				}
				if (!is_array($values) || !count($values)) {
					continue;
				}
				if (!isset($this->data[$index])) {
					$this->data[$index] = array();
				}
				foreach ($values as $value) {
					if (!in_array($value, $this->data[$index])) {
						$this->data[$index] = $value;
					}
				}
			}
		} // end private function add_additional_data
		
		private function get_related_data($post_id, $related_posts) {
			//echo '<pre>'; print_r($related_posts); die;
			if (!count($related_posts)) {
				return;
			}
			//echo '@@ <pre>'; print_r($related_posts); die;
			foreach ($related_posts as $related_post) {
				//echo '<pre>'; print_r($related_post); echo '</pre>';
				//echo '-------------------------------------<br>';
				$hierarchy = $related_post['pointer'];
				$posts = $this->get_related_posts($post_id, $hierarchy);
				//echo '&& <pre>'; print_r($posts);
				if (!count($posts)) {
					// no related posts found
					continue;
				}
				$current_blog = get_current_blog_id();
				$site = intval($related_post['site']);
				//echo $site,', ',$current_blog,'<br><br>';
				if ($site != 0 && $current_blog != $site) {
					switch_to_blog(intval($site));
				}
				foreach ($posts as $related_post_id) {
					// if this is not the same site, clear the cache for this post
					// to avoid possible problems is same id on both sites
					$this->get_fields_data($related_post_id, $related_post['fields']);
				}
				//echo $site,', ',$current_blog,'<br><br>';
				if ($site != 0 && $current_blog != $site) {
					restore_current_blog();
				}
				//echo '-------------------------------------<br>';
			} // end foreach related post
		} // end private function get_related_data
		
		private function get_related_posts($post_id, $hierarchy, $row='') {
			// recursive function
			//echo '%^& <pre>'; print_r($hierarchy); echo '</pre>';
			$posts = array();
			$next = array_shift($hierarchy);
			$row .= $next['name'];
			if (!empty($hierarchy)) {
				$count = intval(get_post_meta($post_id, $row, true));
				for ($i=0; $i<$count; $i++) {
					// recurse
					$posts = array_merge($posts, $this->get_related_posts($post_id, $hierarchy, $row.'_'.$i.'_'));
				}
			} else {
				//echo '<br>',$row,'<br>';
				//echo $values,'<br>';
				$values = get_post_meta($post_id, $row, true);
				//echo $values,'<br><br>';
				$values = maybe_unserialize($values);
				if (!empty($values)) {
					if (!is_array($values)) {
						$values = array($values);
					}
					foreach ($values as $value) {
						$posts[] = $value;
					}
				}
			}
			//print_r($posts);
			return $posts;
		} // end private function get_related_posts
		
		private function get_fields_data($post_id, $fields) {
			// get data listed in fields from $post_id
			if (!count($fields)) {
				return;
			}
			clean_post_cache($post_id);
			$all_meta = get_post_meta($post_id); // force cache
			foreach ($fields as $field) {
				//echo '<pre>'; print_r($field); echo '</pre>';
				$hierarchy = $field['hierarchy'];
				$meta_key = $field['meta_key'];
				$empty = $field['empty'];
				$default = $field['default'];
				if (!isset($this->data[$meta_key])) {
					$this->data[$meta_key] = array();
					$this->clear[] = $meta_key;
				}
				$this->get_field_data($post_id, $hierarchy, $meta_key, $empty, $default);
			} // end foreach $field
			clean_post_cache($post_id);
		} // end private function get_field_data
		
		private function get_field_data($post_id, $hierarchy, $meta_key, $empty, $default, $row='') {
			// recursive function
			//echo get_current_blog_id(),'<br>';
			$next = array_shift($hierarchy);
			$row .= $next['name'];
			//echo $row,'<br>';echo '<pre>'; print_r($hierarchy); echo '<pre>';
			if (!empty($hierarchy)) {
				// there are still sub fields
				$count = intval(get_post_meta($post_id, $row, true));
				//echo $count,'<br>';
				for ($i=0; $i<$count; $i++) {
					// recurse
					$this->get_field_data($post_id, $hierarchy, $meta_key, $empty, $default, $row.'_'.$i.'_');
				}
			} else {
				// end of hierarch, get and store value(s);
				$values = get_post_meta($post_id, $row, true);
				//echo get_current_blog_id(),' => ',$post_id,' => ',$row,' = ',$values,'<br>';
				$values = maybe_unserialize($values);
				if (!is_array($values)) {
					$values = array($values);
				}
				foreach ($values as $value) {
					if (!empty($value) || $empty) {
						if (!in_array($value, $this->data[$meta_key])) {
							$this->data[$meta_key][] = $value;
						}
					}
				}
			} // end if !end or value
		} // end private function get_field_data
		
		private function add_hook() {
			if (!$this->active) {
				return;
			}
			//echo 'add hook ',$this->id,'<br><br>';
			add_action($this->hook, array($this, 'update_post'), $this->priority);
		} // end private function add_hook
		
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
			if (!count($related_post['fields'])) {
				$related_post = array();
			}
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
				$field = $this->build_field($repeater.'_'.$i.'_');
				if (count($field)) {
					$fields[] = $field;
				}
			}
			return $fields;
		} // end private function build_fields
		
		private function build_field($row) {
			$field = array();
			$type = $field['type'] = get_post_meta($this->id, $row.'type', true);
			$hierarchy = array();
			//echo $type, '<br>';
			if ($type == 'serialized' || $type == 'single') {
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
				// remove this, need to build converter even if inactive for nuke
				//return;
			}
			$post_types = get_post_meta($this->id, 'post_types', true);
			if ($post_types === '') {
				$this->active = false;
				//return; removed for nuke
			}
			$post_types = maybe_unserialize($post_types);
			if (!is_array($post_types)) {
				$post_types = array($post_types);
			}
			$this->post_types = $post_types;
			$this->hook = get_post_meta($this->id, 'hook', true);
			if ($this->hook === '') {
				$this->active = false;
				//return; removed for nuke
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
			
			$reparing = intval(get_post_meta($this->id, '_blunt_field_converter_repairing', true));
			if ($reparing) {
				$this->reparing = true;
			}
			$last_repair = get_post_meta($this->id, '_blunt_field_converter_last_repair', true);
			if ($last_repair) {
				$this->last_repair = $last_repair;
			}
			
			$nuking = intval(get_post_meta($this->id, '_blunt_field_converter_nuking', true));
			if ($reparing) {
				$this->nuking = true;
			}
			$last_nuke = get_post_meta($this->id, '_blunt_field_converter_last_nuke', true);
			if ($last_nuke) {
				$this->last_nuke = $last_nuke;
			}
			
		} // end private function build_converter
		
	} // end class blunt_field_converter
	
?>