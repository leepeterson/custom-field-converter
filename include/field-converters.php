<?php 
	
	// If this file is called directly, abort.
	if (!defined('WPINC')) {die;}
	
	new blunt_field_converters();
	
	class blunt_field_converters {
		
		private $converters = array();
		
		public function __construct() {
			add_action('field-converter/init', array($this, 'init'));
		} // end public function __construct
		
		public function init() {
			$this->load_converters();
			//echo '<pre>'; print_r($this->converters); die;
		} // end public function init
		
		private function load_converters() {
			global $post;
			$args = array(
				'post_type' => apply_filters('field-converter/post-type', ''),
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'meta_query' => array(
					array(
						'key' => 'active',
						'value' => '1'
					),
				),
			);
			$query = new WP_Query($args);
			if ($query->have_posts()) {
				while ($query->have_posts()) {
					$query->the_post();
				} // end while have_posts()
				$id = $post->ID;
				$this->converters[$id] = new blunt_field_converter($id);
			} // end if have_posts()
			wp_reset_postdata();
		} // end private function load_converters
		
	} // end class blunt_field_converter
	
?>