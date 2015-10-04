<?php 
	
	// If this file is called directly, abort.
	if (!defined('WPINC')) {die;}
	
	$GLOBALS['blunt_field_converters'] = array();
	
	new blunt_field_converters();
	
	class blunt_field_converters {
		
		private $converters = array();
		
		public function __construct() {
			add_action('field-converter/init', array($this, 'init'));
		} // end public function __construct
		
		public function init() {
			$this->load_converters();
		} // end public function init
		
		private function load_converters() {
			global $post, $blunt_field_converters;
			$args = array(
				'post_type' => apply_filters('field-converter/post-type', ''),
				'post_status' => 'publish',
				'posts_per_page' => -1
			);
			$query = new WP_Query($args);
			if ($query->have_posts()) {
				while ($query->have_posts()) {
					$query->the_post();
					$id = $post->ID;
					//$this->converters[$id] = new blunt_field_converter($id);
					$blunt_field_converters[$id] = new blunt_field_converter($id);
				} // end while have_posts()
			} // end if have_posts()
			wp_reset_postdata();
		} // end private function load_converters
		
	} // end class blunt_field_converter
	
?>