<?php 

	/*
		Plugin Name: Custom Field Converter
		Plugin URI: https://github.com/Hube2/custom-field-converter
		Description: Convert non standard postmeta fields to standard WP postmeta storage
		Version: 0.1.0
		Author: John A. Huebner II
		Author URI: https://github.com/Hube2
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
		} // end public function __construct
		
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
	
?>