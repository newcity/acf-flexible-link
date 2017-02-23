# Flexible Link Field for Advanced Custom Fields

This plugin is an extension for the [Advanced Custom Fields](https://www.advancedcustomfields.com/), version 5.0 or higher. It will do nothing unless the ACF plugin is also installed.

The primary function of the Flexible Link Field is to allow the insertion of either an internal Wordpress post/page, an external URL, or an email address without having to use complicated conditionals or write extra template logic.

## How to use
Install and activate the plugin as you would any other Wordpress plugin, making sure that the ACF Pro plugin is also installed and activated. This will add an entry called "Flexible Link" to the `Field Type` menu when you are creating a new ACF field group.

The Flexible Link field type includes the following settings:

* `Allowed Link Types`: Turn access to different link types on or off in the created field. For example, you may want to enable internal pages and external urls, but disallow e-mail addresses for a particular field.

* `Show link text field`: If enabled, this allows text to be associated with the link. This is particularly useful for buttons.

* `Return Value`: The field can return either an object or a simple string containing a URL. The object contains the following keys:
  * `text`: The text entered in the Link Text field. If `Show link text field` is set to "no," its value will be `false`.
	* `url`: The URL that matches the selected link type. This is also the value that will be returned if `Return Value` is set to `URL only`.
	* `link_type`: The selected link type as a string, *e.g.* 'post', 'url', 'email'

* `Filter Internal Links by Post Type`: If "Internal Post" is one of the allowed link types, this field controls which post types can be selected for that type.

## Acknowledgements
This extension was built with the official [ACF Field Type Template](https://github.com/elliotcondon/acf-field-type-template). It borrows a lot of ideas and some code from the [ACF Smart Button](https://github.com/gillesgoetsch/acf-smart-button) extension.
