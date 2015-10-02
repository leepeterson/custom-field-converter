<?php 
	
	// If this file is called directly, abort.
	if (!defined('WPINC')) {die;}
	
	new blunt_field_converter_ajax();
	
	class blunt_field_converter_ajax {
		
		public function __construct() {
			add_action('wp_ajax_blunt_field_converter_nuke', array($this, 'nuke'));
			add_action('wp_ajax_blunt_field_converter_check_repair', array($this, 'check_repair'));
		} // end public function __construct
		
		public function nuke() {
			if (!current_user_can('update_core') || !isset($_POST['post_id'])) {
				$this->return_json(0, 'nuke', false);
			}
			global $blunt_field_converters;
			$id = $_POST['post_id'];
			$blunt_field_converters[$id]->start_nuke();
			// will only get here if already nuking or reparing
			$this->return_json($id, 'nuke', true);
		} // end public function nuke
		
		public function check_repair() {
			if (!current_user_can('update_core') || !isset($_POST['post_id'])) {
				$this->return_json(0, 'check_repair', false);
			}
			global $blunt_field_converters;
			$id = $_POST['post_id'];
			$blunt_field_converters[$id]->start_check_repair();
			// will only get here if already nuking or reparing
			$this->return_json($id, 'check_repair', true);
		} // end public function check_repair
		
		private function return_json($id, $type, $success) {
			global $blunt_field_converters;
			$name = $blunt_field_converters[$id]->get_name();
			$data = array(
				'success' => $success,
				'type' => $type,
				'id' => $id,
				'name' => $name,
			);
			$json = json_encode($data);
			echo $json;
			exit;
		} // end private function return_json
		
	} // end class blunt_field_converter_ajax
	
?>