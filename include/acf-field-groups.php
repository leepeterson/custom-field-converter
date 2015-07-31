<?php 
	
	// If this file is called directly, abort.
	if (!defined('WPINC')) {die;}
	
	new blunt_field_converter_acf_field_groups();
	
	class blunt_field_converter_acf_field_groups {
		
		public function __construct() {
			add_action('acf/include_fields', array($this, 'acf_include_fields'));
			add_filter('acf/load_field/key=field_01acfcfc00003', array($this, 'acf_load_post_types'));
		} // end public function __construct
		
		public function acf_include_fields() {
			// register field groups
			//echo 'here'; die;
			$this->converter_field_group();
			$this->options_field_group();
		} // end public function acf_include_fields
		
		public function acf_load_post_types($field) {
			$args = array(
				'public' => 'true'
			);
			$args = apply_filters('field-converter/load_field/post_type/args', $args);
			$post_types = get_post_types($args, 'objects');
			$choices = array();
			foreach ($post_types as $post_type) {
				$choices[$post_type->name] = $post_type->label;
			}
			$field['choices'] = $choices;
			return $field;
		} // end public function acf_load_post_types
		
		private function converter_field_group() {
			$field_group = array(
				'key' => 'group_01acfcfc00001',
				'title' => __('Field Converter Settings'),
				'fields' => array(
					array(
						'key' => 'field01acfcfc00029',
						'label' => __('Active'),
						'name' => 'active',
						'type' => 'true_false',
						'instructions' => __('Uncheck this field to deactivate this converter. If you want to delete content that has already been converted use the &quot;NUKE&quot; feature on the options page.'),
						'default_value' => 1,
					),
					array(
						'key' => 'field_01acfcfc00002',
						'label' => __('Settings'),
						'type' => 'tab',
						'placement' => 'top',
						'endpoint' => 1,
					),
					array(
						'key' => 'field_01acfcfc00003',
						'label' => __('Post Types'),
						'name' => 'post_types',
						'type' => 'select',
						'instructions' => __('Select the post types that this converter will run for.'),
						'choices' => array(
							'this will be dynamically generated' => 'this will be dynamically generated',
						),
						'default_value' => array(
							'' => '',
						),
						'allow_null' => 0,
						'multiple' => 1,
					),
					array(
						'key' => 'field_01acfcfc00004',
						'label' => __('Hook'),
						'name' => 'hook',
						'type' => 'radio',
						'instructions' => __('Select the action hook that this converter will use. You can add additional hooks to the list. Please ensure that whatever hook you choose runs after postmeta is saved and that the do_action hook passes the Post ID as the first parameter.'),
						'choices' => array(
							'acf/save_post' => 'acf/save_post',
						),
						'other_choice' => 1,
						'save_other_choice' => 1,
						'default_value' => 'acf/save_post',
						'layout' => 'horizontal',
					),
					array(
						'key' => 'field_01acfcfc00005',
						'label' => __('Prioity'),
						'name' => 'priority',
						'type' => 'number',
						'instructions' => __('Enter the priority to set for this action. The priority should ensure that postmeta values have been saved before the action is called. For example, in ACF a priority of 20 should be used to run after ACF has saved postmeta to the database. This setting is made available for users that may be using a different custom field plugin that requires a different priority for a different hook. If you are using ACF you should not edit this setting.'),
						'default_value' => 20,
						'step' => 1,
					),
					array(
						'key' => 'field_01acfcfc00006',
						'label' => __('Fields'),
						'type' => 'tab',
						'placement' => 'top',
					),
					array(
						'key' => 'field_01acfcfc00007',
						'label' => __('Fields'),
						'name' => 'fields',
						'type' => 'repeater',
						'instructions' => __('Add the fields that you want to convert.'),
						'layout' => 'row',
						'button_label' => __('Add Field'),
						'sub_fields' => array(
							array(
								'key' => 'field_01acfcfc00008',
								'label' => __('Field Type'),
								'name' => 'type',
								'type' => 'radio',
								'instructions' => '',
								'choices' => array(
									'serialized' => __('Serialized Array Field'),
									'nested' => __('ACF Repeater or Flexible Content Sub Field'),
								),
								'default_value' => 'serialized',
								'layout' => 'vertical',
							),
							array(
								'key' => 'field_01acfcfc00009',
								'label' => __('Field Name'),
								'name' => 'name',
								'type' => 'text',
								'instructions' => __('Enter the meta_key for this field.'),
								'required' => 1,
								'conditional_logic' => array(
									array(
										array(
											'field' => 'field_01acfcfc00008',
											'operator' => '==',
											'value' => 'serialized',
										),
									),
								),
							),
							array(
								'key' => 'field_01acfcfc00010',
								'label' => __('Field Hierarchy'),
								'name' => 'hierarchy',
								'type' => 'repeater',
								'instructions' => __('Create a row for each sub field in the field hierarchy. End the list with the field that will be converted.'),
								'conditional_logic' => array(
									array(
										array(
											'field' => 'field_01acfcfc00008',
											'operator' => '==',
											'value' => 'nested',
										),
									),
								),
								'min' => 2,
								'layout' => 'table',
								'button_label' => __('Add Field'),
								'sub_fields' => array(
									array(
										'key' => 'field_01acfcfc00011',
										'label' => __('Field Name'),
										'name' => 'name',
										'type' => 'text',
										'instructions' => '',
										'required' => 1,
									),
									array(
										'key' => 'field_01acfcfc00012',
										'label' => __('Field Type'),
										'name' => 'type',
										'type' => 'select',
										'instructions' => '',
										'choices' => array(
											'repeater' => __('Repeater'),
											'flex' => __('Flexible Content'),
											'single' => __('Single Value'),
											'serialized' => __('Serialized Array'),
										),
										'default_value' => array(
											'repeater' => 'repeater',
										),
									),
								),
							),
							array(
								'key' => 'field_01acfcfc00013',
								'label' => __('New Field meta_key'),
								'name' => 'meta_key',
								'type' => 'text',
								'instructions' => __('Enter the meta_key name for the converted data. Ensure that this meta_key is unique for the post type.'),
								'required' => 1,
								'maxlength' => 64,
							),
						),
					),
					array(
						'key' => 'field_01acfcfc00014',
						'label' => __('Related Posts'),
						'type' => 'tab',
						'placement' => 'top',
						'endpoint' => 0,
					),
					array(
						'key' => 'field_01acfcfc00015',
						'label' => __('Converting Related Post Data'),
						'type' => 'message',
						'message' => __('Adding related posts to convert copies the custom fields from the related post and adds it to the custom field content of the post to allow these fields to be searched as if they are part of the post.'),
						'esc_html' => 0,
					),
					array(
						'key' => 'field_01acfcfc00016',
						'label' => __('Related Posts'),
						'name' => 'related_posts',
						'type' => 'repeater',
						'instructions' => __('Enter the hierarchy of the field that holds the pointer(s) to the related post in the main post. This field must contain either a single post ID or multiple post IDs (can be serialized) for the related posts.'),
						'layout' => 'row',
						'button_label' => __('Add Related Post Field'),
						'sub_fields' => array(
							array(
								'key' => 'field_01acfcfc00017',
								'label' => __('Pointer Field'),
								'type' => 'tab',
								'placement' => 'top',
								'endpoint' => 0,
							),
							array(
								'key' => 'field_01acfcfc00018',
								'label' => __('Pointer Field Hierarchy'),
								'name' => 'pointer',
								'type' => 'repeater',
								'instructions' => __('Enter the hierarchy of the field that holds the pointer to the related post in the main post. This field must contain either a single post ID or multiple post IDs (can be serialized) for the related posts.'),
								'layout' => 'table',
								'button_label' => __('Add Sub Field'),
								'min' => 1,
								'sub_fields' => array(
									array(
										'key' => 'field_01acfcfc00019',
										'label' => 'Field Name',
										'name' => 'name',
										'type' => 'text',
										'instructions' => __('This is the field that contains the pointer to your related posts'),
										'required' => 1,
									),
									array(
										'key' => 'field_01acfcfc00020',
										'label' => __('Field Type'),
										'name' => 'type',
										'type' => 'select',
										'instructions' => '',
										'choices' => array(
											'repeater' => __('Repeater'),
											'flex' => __('Flexible Content'),
											'single' => __('Single Value'),
											'serialized' => __('Serialized Array'),
										),
										'default_value' => array(
											'single' => 'single',
										),
									),
								),
							),
							array(
								'key' => 'field_01acfcfc00021',
								'label' => __('Fields'),
								'type' => 'tab',
								'placement' => 'top',
								'endpoint' => 0,
							),
							array(
								'key' => 'field_01acfcfc00022',
								'label' => __('Related Post Fields'),
								'name' => 'fields',
								'type' => 'repeater',
								'instructions' => __('Add the fields that you want to convert.'),
								'required' => 0,
								'min' => 1,
								'layout' => 'row',
								'button_label' => __('Add Field'),
								'sub_fields' => array(
									array(
										'key' => 'field_01acfcfc00023',
										'label' => __('Field Type'),
										'name' => 'type',
										'type' => 'radio',
										'instructions' => '',
										'choices' => array(
											'serialized' => __('Serialized Array Field'),
											'nested' => __('ACF Repeater or Flexible Content Sub Field'),
										),
										'default_value' => 'serialized',
										'layout' => 'vertical',
									),
									array(
										'key' => 'field_01acfcfc00024',
										'label' => __('Field Name'),
										'name' => 'name',
										'type' => 'text',
										'instructions' => __('Enter the meta_key for this field.'),
										'required' => 1,
										'conditional_logic' => array(
											array(
												array(
													'field' => 'field_01acfcfc00023',
													'operator' => '==',
													'value' => 'serialized',
												),
											),
										),
									),
									array(
										'key' => 'field_01acfcfc00025',
										'label' => __('Field Hierarchy'),
										'name' => 'hierarchy',
										'type' => 'repeater',
										'instructions' => __('Create a row for each sub field in the field hierarchy. End the list with the field that will be converted.'),
										'conditional_logic' => array(
											array(
												array(
													'field' => 'field_01acfcfc00023',
													'operator' => '==',
													'value' => 'nested',
												),
											),
										),
										'min' => 2,
										'layout' => 'table',
										'button_label' => __('Add Field'),
										'sub_fields' => array(
											array(
												'key' => 'field_01acfcfc00026',
												'label' => __('Field Name'),
												'name' => 'name',
												'type' => 'text',
												'instructions' => '',
												'required' => 1,
											),
											array(
												'key' => 'field_01acfcfc00027',
												'label' => __('Field Type'),
												'name' => 'type',
												'type' => 'select',
												'instructions' => '',
												'choices' => array(
													'repeater' => __('Repeater'),
													'flex' => __('Flexible Content'),
													'single' => __('Single Value'),
													'serialized' => __('Serialized Array'),
												),
												'default_value' => array(
													'repeater' => 'repeater',
												),
											),
										),
									),
									array(
										'key' => 'field_01acfcfc00028',
										'label' => __('New Field meta_key'),
										'name' => 'meta_key',
										'type' => 'text',
										'instructions' => __('Enter the meta_key name for the converted data. Ensure that this meta_key is unique for the post type.'),
										'required' => 1,
										'maxlength' => 64,
									),
								),
							),
						),
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => apply_filters('field-converter/post-type', ''),
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => array(
					0 => 'permalink',
					1 => 'the_content',
					2 => 'excerpt',
					3 => 'custom_fields',
					4 => 'discussion',
					5 => 'comments',
					6 => 'slug',
					7 => 'author',
					8 => 'format',
					9 => 'page_attributes',
					10 => 'featured_image',
					11 => 'categories',
					12 => 'tags',
					13 => 'send-trackbacks',
				),
			);
			acf_add_local_field_group($field_group);
		} // end private function converter_field_group
		
		private function options_field_group() {
			
		} // end private function options_field_group
		
	} // end class blunt_field_converter_acf_field_groups
	
?>