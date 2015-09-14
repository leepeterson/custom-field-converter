<?php 
	
	new hook_test();
	
	class hook_test {
		
		public function __construct() {
			add_action('init', array($this, 'init_10'));
		} // end public function __construct
		
		public function __call($function, $args) {
			//echo current_filter(),'<br />';
			//echo $function; die;
		} // end public function __call
		
	} // end class hook_test
	
?>