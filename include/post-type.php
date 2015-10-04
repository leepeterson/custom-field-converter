<?php 
	
	// If this file is called directly, abort.
	if (!defined('WPINC')) {die;}
	
	new blunt_field_converter_post_type();
	
	class blunt_field_converter_post_type {
		
		private $post_type = 'field-converter';
		
		public function __construct() {
			add_action('field-converter/init', array($this, 'init'), 1);
			add_action('field-converter/post-type/label', array($this, 'label'));;
			add_action('field-converter/post-type/labels', array($this, 'labels'));;
			add_filter('field-converter/post-type', array($this, 'post_type'));
		} // end public function __construct
		
		public function label($label) {
			$label = __('Field Converters');
			return $label;
		} // end public function label
		
		public function labels($labels) {
			$labels = array (
				'name' => __('Field Converters'),
				'singular_name' => __('Field Converter'),
				'menu_name' => __('Field Converters'),
				'add_new' => __('Add Field Converter'),
				'add_new_item' => __('Add New Field Converter'),
				'edit' => __('Edit'),
				'edit_item' => __('Edit Field Converter'),
				'new_item' => __('New Field Converter'),
				'view' => __('View Field Converter'),
				'view_item' => __('View Field Converter'),
				'search_items' => __('Search Field Converters'),
				'not_found' => __('No Field Converters Found'),
				'not_found_in_trash' => __('No Field Converters Found in Trash'),
				'parent' => __('Parent Field Converter')
			);
			return $labels;
		} // end public function labels
		
		public function post_type($post_type) {
			return $this->post_type;
		} // end public function post_type
		
		public function init() {
			// register the post type
			$capabilities = array(
				'edit_post' => 'update_core',
				'read_post' => 'update_core',
				'delete_post' => 'update_core',
				'edit_others_posts' => 'update_core',
				'delete_posts' => 'update_core',
				'publish_posts' => 'update_core',
				'read_private_posts' => 'update_core'
			);
			$capabilities = apply_filters('field-converter/post-type/capabilities', $capabilities);
			$post_type = apply_filters('field-converter/post-type', '');
			$args = array(
				'label' => apply_filters('field-converter/post-type/label', ''),
				'description' => '',
				'public' => false,
				'show_ui' => true,
				'show_in_menu' => true,
				'menu_icon' => 'dashicons-admin-generic',
				'show_in_admin_bar' => false,
				//'capability_type' => 'post',
				'map_meta_cap' => true,
				'hierarchical' => false,
				'rewrite' => array(
					'slug' => $post_type, 
					'with_front' => true,
					'pages' => false,
				),
				'query_var' => true,
				'exclude_from_search' => true,
				'menu_position' => 100,
				'supports' => array(
					'title', 
					'custom-fields', 
					'revisions'
				),
				'labels' => apply_filters('field-converter/post-type/labels', array()),
				'capabilities' => $capabilities,
			);
			register_post_type($post_type, $args);
		} // end public function init
		
	} // end class blunt_field_converter_post_type
	
	
?>