# Custom Field Converter

Convert custom fields stored in no WP postmeta DB storage format to standard WP storage format.

Requires ACF Pro & Blunt Ajax

Custom field plugin can store data in the DB in non-standard ways.

The standard storage method for WO to store multiple values for a single `meta_key` is to use multiple DB rows with the same `meta_key`.

Some custom field plugins store data in serialized arrays. This data storage makes it difficult to do queries with `WP_Query`.

This plugin will convert data of these fields to standard storage formate using a differnt `meta_key` to make the values them easier to query.
