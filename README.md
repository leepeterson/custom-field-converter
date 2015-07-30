# Custom Field Converter

***This is a work in progress, it is not complete and should not be uses. It will not work yet!***

Convert custom fields stored in formats other that standard WP postmeta DB storage format into standard WP
storage format.

Examples of fields that can be converet include
* Fields stored as serialized data
* ACF Repeater Sub Fields
* ACF Flexible Content Sub Fields

Requires 
ACF Pro: http://www.advancedcustomfields.com/pro/
Blunt Ajax: https://wordpress.org/plugins/blunt-ajax/

Custom field plugins can store data in the DB in non-standard ways which make it difficult to query this
data using a standard WP_Query.

The standard storage method that WP uses to store multiple values for a single `meta_key` is to use multiple 
DB rows with the same `meta_key` and a different value for each.

This plugin will convert data of these fields to standard storage format using a differnt `meta_key` to make 
the values in them easier to query.

This plugin assumes that the user understands how data is stored in different types of custom fields.
