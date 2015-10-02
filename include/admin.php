<?php 
	
	// If this file is called directly, abort.
	if (!defined('WPINC')) {die;}
	
	new blunt_field_converter_admin();
	
	class blunt_field_converter_admin {
		
		public function __construct() {
			add_action('field-converter/init', array($this, 'init'));
			add_action('admin_enqueue_scripts', array($this, 'enqueue'));
		} // end public function __construct
		
		public function enqueue() {
			$screen = get_current_screen();
			if ($screen->id != 'edit-acf-field-converter') {
				return;
			}
			//echo '<!-- '; print_r($screen); echo ' -->';
			wp_enqueue_style(
				'field-converter-styles',
				dirname(plugin_dir_url(__FILE__)).'/css/field-converter.css'
			);
			wp_enqueue_script(
				'field-converter-script', 
			 	dirname(plugin_dir_url(__FILE__)).'/js/field-converter.js',
				array('jquery'), 
				'', 
				true);
			wp_localize_script(
				'field-converter-script', 
				'blunt_converter_ajax_object',
				array('ajax_url' => admin_url('admin-ajax.php'))
			);
		} // end public function enqueue
		
		public function init() {
			$post_type = apply_filters('field-converter/post-type', '');
			add_filter('manage_edit-'.$post_type.'_columns', array($this, 'admin_columns'));
			add_action('manage_'.$post_type.'_posts_custom_column', array($this, 'admin_columns_content'), 10, 2 );
		} // end public function init
		
		public function admin_columns($columns) {
			$new_columns = array();
			foreach ($columns as $index => $column) {
				if (strtolower($column) == __('title')) {
					$new_columns[$index] = $column;
					$new_columns['active'] = __('Active');
					$new_columns['check'] = __('Check &amp; Repair');
					$new_columns['nuke'] = __('Nuke');
				} else {
					$new_columns[$index] = $column;
				}
			}
			return $new_columns;
		} // end public function admin_columns
		
		public function admin_columns_content($column, $post_id) {
			global $blunt_field_converters;
			//echo $post_id,'<pre>'; print_r($blunt_field_converters); echo '</pre>';
			switch ($column) {
				case 'active':
					$active = $blunt_field_converters[$post_id]->get_active();
					if ($active) {
						echo '<strong style="color:#080">'.__('Yes').'</strong>';
					} else {
						echo '<strong style="color:#800">'.__('No').'</strong>';
					}
					break;
				case 'check':
					$repairing = $blunt_field_converters[$post_id]->get_reparing();
					$nuking = $blunt_field_converters[$post_id]->get_nuking();
					$active = $blunt_field_converters[$post_id]->get_active();
					if ($repairing[0]) {
						echo '<strong style="color:#800">'.
									__('**CHECKING &amp; REPAIRING! **').'</strong>';
					} elseif ($nuking[0]) {
						echo '&nbsp;';
					} elseif (!$active) {
						echo '<strong style="color:#800">'.
									__('Not Active, Cannot Check &amp; Repair').'</strong>';
					} else {
						?>
							<a href="javascript: blunt_field_converter_check_repair(<?php 
								echo $post_id; ?>)" class="field-converter button orange"><?php 
								echo __('CHECK &amp; REPAIR'); ?></a><br />
							<strong>Last Checked:</strong> <?php echo $repairing[1]; ?>
						<?php 
					}
					break;
				case 'nuke':
					$repairing = $blunt_field_converters[$post_id]->get_reparing();
					$nuking = $blunt_field_converters[$post_id]->get_nuking();
					if ($repairing[0]) {
						echo '&nbsp;';
					} elseif ($nuking[0]) {
						echo '<strong style="color:#800">'.
									__('** NUKING! **').'</strong>';
					} else {
						?>
							<a href="javascript: blunt_field_converter_nuke(<?php 
								echo $post_id; ?>)" class="field-converter button red"><?php 
									echo __('NUKE'); ?></a><br />
							<strong>Last Nuked:</strong> <?php echo $nuking[1]; ?>
						<?php 
					}
					break;
			} // end switch ($column)
		} // end public function admin_columns_content
		
	} // end class blunt_field_converter_admin
	
?>