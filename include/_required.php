<?php 
	
	// If this file is called directly, abort.
	if (!defined('WPINC')) {die;}
	
	$converter_file_base = dirname(__FILE__);
	require($converter_file_base.'/options-page.php');
	require($converter_file_base.'/post-type.php');
	require($converter_file_base.'/settings.php');
	require($converter_file_base.'/acf-field-groups.php');
	require($converter_file_base.'/field-converters.php');
	require($converter_file_base.'/field-converter.php');
	require($converter_file_base.'/admin.php');
	require($converter_file_base.'/ajax.php');
	
	
	// for testing only
	//require(dirname(dirname(__FILE__)).'/__testing/functions.php');
	
?>