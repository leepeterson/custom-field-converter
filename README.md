# Custom Field Converter

This plugin requires that Advanced Custom Fields 5 Pro (ACF). This plugin will not provide any
funtionality if ACF is not installed.

***Please submit bugs, problems, comments & questions [here](https://github.com/Hube2/custom-field-converter/issues).***

Convert custom fields stored in formats other that standard WP postmeta DB storage format into standard WP
storage format.

Examples of fields that can be converet include
* Fields stored as serialized data
* ACF Repeater Sub Fields
* ACF Flexible Content Sub Fields
* Copy & Convert fields in related posts so that they can be searched as if part of other posts

Requires: 
* ACF Pro: http://www.advancedcustomfields.com/pro/

Custom field plugins can store data in the DB in non-standard ways which make it difficult to query this
data using a standard `WP_Query()`.

The standard storage method that WP uses to store multiple values for a single `meta_key` is to use multiple
DB rows with the same `meta_key` and a different value for each.

This plugin will convert data in non standard fields to standard storage format into a differnt `meta_key` 
to make the values in them easier to query. This requires using the new meta key that you assign to these
values.

This plugin assumes that the user understands how data is stored in different types of custom fields.

##Usefull For
* Converting multiselect to standard WP storage
* Converting checkbox fields to standard WP storage
* Converting other fields stored as serialized arrays into standard WP storage format
* Converting ACF repeater fields to standard WP storage
* Copying custom fields from related posts to allow easier searching of posts by related content

##Not Usefull For
* Repeater Field Matching: If you need to search repeater field rows and match fields in the row 
(For example, `WHERE repeater_1_number = "10" AND repeater_1_text = "Bob"`) then this plugin will 
not help you. I do not plan to build in this functionality so please do not ask. This may be 
possible but you'll need to build it yourself for your own particlar needs. I have provided a 
filter that will allow you to add additional fields, see filters below.

##Check & Repair
The check and repair feature checks all posts associated with a converter and performs the convert 
on all the posts. This is usefull if you think there may be problems with the converted data or you 
have change the converter after adding posts and you need to make the changes effect these previous posts.

##Nuke
The nuke feature allows you to delete all converted custom fields for all posts that are 
associated with the converter.

##Hooks

###Filters

####field_converter/additional_data
This filter allows you to build your own additional custom data to be added to each post that is beyond the capabilities of this plugin. For example you could include custom fields from a taxonomy or some other source. Data must be returned in a nested array. See the example.

Example:
```
add_filter('field_converter/additional_data', 'my_custom_data_filter');
function my_custom_data_filter($data) {
  $data = array(
    'my_extra_field_1' => array(
      'value 1',
      'value 2'
    ),
    'my_extra_field_2' => array(
      'value 1',
      'value 2',
      'value 3'
    )
  );
  return $data;
}
```


####field-converter/load_field/post_type/args

This filter allows you do allter the post types loaded into the post type selection field of the field converter settings. These are the arguments used to get the list of post types and it conforms to the format of the wordpress function [get_post_types()](https://codex.wordpress.org/Function_Reference/get_post_types)

The default values of $args:
```
$args = array(
  'public' => 'true'
);
```
Example:
```
add_filter('my-converter-post-type-args', 'converter_post_type_args');
function converter_post_type_args($args) {
  $args['public'] = false;
  return $args;
}
```


####field-converter/post-type/capabilities
This filter allows you to change the capablities for viewing and editing field converters. The default value for all arguments is `update_core` meaning that only an admin user, or the super admin user on multisite, can edit or view field converters. You can change this if you want other types of users to have access. Additional capabilites not listed can also be added if you choose.

The default values are:
```
$capabilities = array(
  'edit_post' => 'update_core',
  'read_post' => 'update_core',
  'delete_post' => 'update_core',
  'edit_others_posts' => 'update_core',
  'delete_posts' => 'update_core',
  'publish_posts' => 'update_core',
  'read_private_posts' => 'update_core'
);
```
Example:
```
add_filter('field-converter/post-type/capabilities', 'my_converter_capabilities');
function my_converter_capabilities($capabilities) {
  // set to standard post capabilities
	$capabilities = array(
		'edit_post' => 'edit_posts',
		'read_post' => 'read',
		'delete_post' => 'delete_posts',
		'edit_others_posts' => 'edit_others_posts',
		'delete_posts' => 'delete_posts',
		'publish_posts' => 'publish_posts',
		'read_private_posts' => 'read_private_posts'
	);
	return $capabilities;
}
```


####field-converter/repair_capibility
This filter sets the capability required to initiate the **Check & Repair** feature. The default value for this feature is `update_core` meaning that only an admin user, or the super admin user on multisite, can initiate a the feature. You can change this if you want other types of users to have access to this feature.

Example:
```
add_filter('field-converter/repair_capibility', 'my_repair_capibility', 20);
my_repair_capibility($capability) {
  return 'edit_posts';
}

```


####field-converter/nuke_capibility
This filter sets the capability required to initiate the **Nuke** feature. The default value for this feature is `update_core` meaning that only an admin user, or the super admin user on multisite, can initiate a the feature. You can change this if you want other types of users to have access to this feature.

Example:
```
add_filter('field-converter/nuke_capibility', 'my_nuke_capibility', 20);
my_nuke_capibility($capability) {
  return 'edit_posts';
}

```

