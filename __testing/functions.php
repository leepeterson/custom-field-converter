<?php 
	
	/*
			This is a test file only
			This filter loads a field that I created to select cross site related posts
			to test cross site data importing
	*/
	
	add_filter('acf/load_field/name=related_specs_cross_site', 'acf_load_related_specs_cross_site');
	
	function acf_load_related_specs_cross_site($field) {
		switch_to_blog(2);
		$choices = array();
		$args = array(
			'post_type' => 'product-specs',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'order' => 'ASC',
			'orderby' => 'title'
		);
		$query = new WP_Query($args);
		//echo '<pre>'; print_r($query->posts); echo '</pre>';
		if ($query->have_posts()) {
			global $post;
			while($query->have_posts()) {
				$query->the_post();
				$choices[$post->ID] = $post->post_title;
			}
		}
		restore_current_blog();
		wp_reset_postdata();
		$field['choices'] = $choices;
		return $field;
	} // end acf_load_related_specs_cross_site
	
?>