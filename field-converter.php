<?php 

	/*
		Plugin Name: Custom Field Converter
		Plugin URI: https://github.com/Hube2/custom-field-converter
		Description: Convert non standard postmeta fields to standard WP postmeta storage
		Version: 1.0.0
		Author: John A. Huebner II
		Author URI: https://github.com/Hube2
		GitHub Plugin URI: https://github.com/Hube2/custom-field-converter
		License: GPL v2 or later
		
		This plugin is used to convert serailized arrays and other complex data types into standard WP
		database storage where each postmets meta_key may be stored in mulitple database rows. This
		makes it easier to use standard WP searching mechanisims, like WP_Query, to search these posts.
		
		This plugin requires:
			ACF Pro: http://www.advancedcustomfields.com/pro/
		
		This plugin will not provide any functionality if ACF is not installed
		
	*/
	
	// If this file is called directly, abort.
	if (!defined('WPINC')) {die;}
	
	require(dirname(__FILE__).'/include/_required.php');
	
	new blunt_custom_field_converter();
	
	class blunt_custom_field_converter {
		
		public function __construct() {
			register_activation_hook(__FILE__, array($this, 'activate'));
			register_deactivation_hook(__FILE__, array($this, 'deactivate'));
			add_action('init', array($this, 'init'));
			add_filter('jh_plugins_list', array($this, 'meta_box_data'));
			add_action('admin_head', array($this, 'admin_head'));
		} // end public function __construct
		
		public function admin_head() {
			//echo '<pre>'; print_r(get_current_screen()); die;
		} // end public function admin_head
			
			function meta_box_data($plugins=array()) {
				
				$plugins[] = array(
					'title' => 'Custom Field Converter for ACF',
					'screens' => array('edit-field-converter', 'field-converter'),
					'doc' => 'https://github.com/Hube2/custom-field-converter'
				);
				return $plugins;
				
			} // end function meta_box
		
		public function activate() {
			// just in case I need to do something to activate
		} // end public function activate
		
		public function deactivate() {
			// just in case I need to do something to deactivate
		} // end public function deactivate
		
		public function init() {
			// runs on WP init hook
			do_action('field-converter/init');
		} // end public function init
		
	} // end class blunt_custom_field_converter
	
	if (!function_exists('jh_plugins_list_meta_box')) {
		function jh_plugins_list_meta_box() {
			$plugins = apply_filters('jh_plugins_list', array());
				
			$id = 'plugins-by-john-huebner';
			$title = '<a style="text-decoration: none; font-size: 1em;" href="https://github.com/Hube2" target="_blank">Plugins by John Huebner</a>';
			$callback = 'show_blunt_plugins_list_meta_box';
			$screens = array();
			foreach ($plugins as $plugin) {
				$screens = array_merge($screens, $plugin['screens']);
			}
			$context = 'side';
			$priority = 'low';
			add_meta_box($id, $title, $callback, $screens, $context, $priority);
			
			
		} // end function jh_plugins_list_meta_box
		add_action('add_meta_boxes', 'jh_plugins_list_meta_box');
			
		function show_blunt_plugins_list_meta_box() {
			$plugins = apply_filters('jh_plugins_list', array());
			?>
				<p style="margin-bottom: 0;">Thank you for using my plugins</p>
				<ul style="margin-top: 0; margin-left: 1em;">
					<?php 
						foreach ($plugins as $plugin) {
							?>
								<li style="list-style-type: disc; list-style-position:">
									<?php 
										echo $plugin['title'];
										if ($plugin['doc']) {
											?> <a href="<?php echo $plugin['doc']; ?>" target="_blank">Documentation</a><?php 
										}
									?>
								</li>
							<?php 
						}
					?>
				</ul>
				<p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=hube02%40earthlink%2enet&lc=US&item_name=Donation%20for%20WP%20Plugins%20I%20Use&no_note=0&cn=Add%20special%20instructions%20to%20the%20seller%3a&no_shipping=1&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted" target="_blank">Please consider making a small donation.</a></p><?php 
		}
	} // end if !function_exists

?>