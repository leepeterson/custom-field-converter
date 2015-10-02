// JavaScript Document

function blunt_field_converter_ajax_callback(response) {
	alert(response);
} // end function blunt_field_converter_ajax_callback

function blunt_field_converter_nuke(id) {
	jQuery.post(
		blunt_converter_ajax_object.ajax_url, {
				'action': 'blunt_field_converter_nuke',
				'post_id': id
			},
		function (data) {
			blunt_field_converter_ajax_callback(data)
		}
	);
} // end function blunt_field_converter_nuke

function blunt_field_converter_check_repair(id) {
	jQuery.post(
		blunt_converter_ajax_object.ajax_url, {
				'action': 'blunt_field_converter_check_repair',
				'post_id': id
			},
		function (data) {
			blunt_field_converter_ajax_callback(data)
		}
	);
} //function blunt_field_converter_check_repair