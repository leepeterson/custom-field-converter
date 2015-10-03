# Custom Field Converter

This plugin requires that Advanced Custom Fields 5 Pro (ACF) in installed. This plugin will not provide any
funtionality if ACF is not installed.

***This plugin needs testing. Please submit bugs, problems, comment & questions [here](https://github.com/Hube2/custom-field-converter/issues)***

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


