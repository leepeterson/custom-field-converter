<?php 
	
	// If this file is called directly, abort.
	if (!defined('WPINC')) {die;}
	
	new blunt_field_converter_options_page();
	
	class blunt_field_converter_options_page {
		
		private $slug = 'field-converter';
		
		public function __construct() {
			add_action('field-converter/init', array($this, 'init'));
		} // end public function __construct
		
		public function init() {
			if (!function_exists('acf_add_options_sub_page')) {
				return;
			}
			$page = array(
				'title' => __('Field Converter Settings'),
				'menu' => __('Converter Options'),
				'capability' => 'manage_options',
				'parent' => 'edit.php?post_type='.apply_filters('field-converter/post-type', ''),
				'slug' => $this->slug
			);
			acf_add_options_sub_page($page);
		} // end public function init
		
	} // end class blunt_field_converter_options_page
	
?>