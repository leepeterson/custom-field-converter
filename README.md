# Custom Field Converter

This plugin requires that Advanced Custom Fields 5 Pro (ACF) in installed. This plugin will not provide any
funtionality if ACF is not installed.

***This plugin needs testing. Please submit bugs, problems, comment & questions [here](https://github.com/Hube2/custom-field-converter/issues).***

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

This plugin will convert data of these fields to standard storage format using a differnt `meta_key` to make 
the values in them easier to query.

This plugin assumes that the user understands how data is stored in different types of custom fields.

##Usefull For
* Converting multiselect to standard WP storage
* Converting checkbox fields to standard WP storage
* Converting repeater fields to standard WP storage
* Copying custom fields from related posts to allow searching of posts by related content

##Not Usefull For
* Repeater Field Matching: If you need to search repeater field rows and match fields in the row (For example, `WHERE repeater_1_number = "10" AND repeater_1_text = "Bob"`) then this plugin will not help you. I do not plan to build in this functionality so please do not ask.

##Hooks

###Filters

**field-converter/load_field/post_type/args**

This filter allows you do allter the post types loaded into the post type selection field of the field converter settings. These are the arguments used to get the list of post types and it conforms to the format of the wordpress function [get_post_types()](https://codex.wordpress.org/Function_Reference/get_post_types)


