<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('acf_field_flexible_link') ) :


class acf_field_flexible_link extends acf_field {


	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function __construct( $settings ) {

		/*
		*  name (string) Single word, no spaces. Underscores allowed
		*/

		$this->name = 'flexible_link';


		/*
		*  label (string) Multiple words, can include spaces, visible when selecting a field type
		*/

		$this->label = __('Flexible Link', 'acf-flexible-link');


		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/

		$this->category = 'basic';


		/*
		*  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		*/

		$this->defaults = array(
			'allowed_link_types' => array(
				'post',
				'url',
				'email',
			),
			'show_text' => true,
			'default_link_type' => 'post',
			'default_text' => ''
		);


		/*
		*  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		*  var message = acf._e('flexible_link', 'error');
		*/

		$this->l10n = array(
			'error'	=> __('Error! Please enter a higher value', 'acf-flexible-link'),
		);


		/*
		*  settings (array) Store plugin settings (url, path, version) as a reference for later use with assets
		*/

		$this->settings = $settings;


		// do not delete!
    	parent::__construct();

	}


	/*
	*  render_field_settings()
	*
	*  Create extra settings for your field. These are visible when editing a field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/

	function render_field_settings( $field ) {

		/*
		*  acf_render_field_setting
		*
		*  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
		*  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
		*
		*  More than one setting can be added by copy/paste the above code.
		*  Please note that you must also have a matching $defaults value for the field name (font_size)
		*/
		$field = array_merge($this->defaults, $field);

		acf_render_field_setting( $field, array(
			'label'			=> __('Allowed Link Types','acf-flexible-link'),
			'instructions'	=> __('Link types available for use with this field','acf-flexible-link'),
			'type'			=> 'checkbox',
			'name'			=> 'allowed_link_types',
			// 'prepend'		=> 'px',
			'choices'		=> array(
				'post'		=>	__('Internal Post', 'acf-flexible-link'),
				'url'		=>	__('External URL', 'acf-flexible-link'),
				'email'		=>	__('E-Mail Address', 'acf-flexible-link'),
			)
		), true);

		acf_render_field_setting( $field, array(
			'label'			=> __('Show link text field', 'acf-flexible-link'),
			'instructions'	=> '',
			'name'			=> 'show_text',
			'type'			=> 'true_false',
			'ui'			=> 1,
		), true);

		acf_render_field_setting( $field, array(
			'label'			=> __('Return value:', 'acf-flexible-link'),
			'instructions'	=> '',
			'name'			=> 'return_type',
			'type'			=> 'radio',
			'choices'			=> array(
				0		=>	__('Object', 'acf-flexible-link'),
				1		=>	__('URL only', 'acf-flexible-link')
			),
		), true);

		acf_render_field_setting( $field, array(
			'label'	=> __('Filter Internal Links by Post Type','acf'),
			'instructions' => '',
			'type' => 'select',
			'name' => 'post_type',
			'choices' => acf_get_pretty_post_types(),
			'multiple' => 1,
			'ui' => 1,
			'allow_null' => 1,
			'placeholder' => __("All post types",'acf'),
		));

	}

function html_text( $field ) {
	if ( $field['show_text'] ) {
		$label = '<div class="acf-field acf-field-text"><div class="acf-label"><label style="font-weight: normal;" for="' . esc_attr($field["id"]) . '">Text</label></div>';
		$input = '<div class="acf-input-wrap"><input type="text" name="' . esc_attr($field["name"]) . '[text]' . '" id="' . esc_attr($field["id"]) . '[text]" value="' . esc_attr($field["value"]["text"]) . '" /></div></div>';
		return $label . $input;
	}

	return '';
}

function html_link_choices( $field ) {

	$labels = array (
		'post' => 'Internal Link',
		'url' => 'External Link',
		'email' => 'E-Mail Address'
	);

	if ( count( $field['allowed_link_types'] ) > 1 ) {
		$options = '<div class="acf-field acf-field-radio" style="margin-right: 1em;"><div class="acf-label"><label style="font-weight: normal;">Link Type</label></div><ul class="acf-radio-list acf-vl">';
		foreach( $field['allowed_link_types'] as $value) {
			$label_class = 'link_type_picker';
			$checked = '';

			if( is_array($field['value']) && array_key_exists( 'link_type', $field['value'] ) ) {
				$current_type = $field['value']['link_type'];
			} else {
				$current_type = $field['link_type'];
			}
			if ( $current_type == $value) {
				$label_class .= ' selected';
				$checked = 'checked';
			}

			$options .= '<li><label class="' . $label_class . '"><input type="radio" name="' . esc_attr($field["name"]) . '[link_type]" id="' . esc_attr($field["id"]) . '[link_type]" value="' . $value . '" ' . $checked . '> ' . $labels[$value] . '</label></li>';
		}
		$options .= '</ul></div>';
		return $options;
	}
	return false;
}

function html_link_fields($field, $field_width_style ) {
	$field_name = esc_attr( $field['name'] );
	$field_raw_key = str_replace("field_", "", $field["key"]);
	$types = array("post", "page");
	$field_classes = array(
		'url' => 'acf-hidden',
		'post' => 'acf-hidden',
		'email' => 'acf-hidden'
	);

	$field_classes[ $field['value']['link_type'] ] = '';


	?>

	<div class="acf-field acf-field-url <?php echo $field_classes['url'] ?>" style="<?php echo $field_width_style ?>">
	<div class="acf-label">
		<label style="font-weight: normal;">External Link</label>
	</div>
	<?php

		do_action('acf/render_field/type=url', array(
			'type' => 'url',
			'name' => $field_name . '[external_url]',
			'value' => $field['value']['external_url'],
			'id' => $field['id'] .'[external_url]',
			'class' => '',
			'placeholder' => ''
		));
	?>
</div>

	<?php
		// str replace to get raw key (there seems to be no other way?)
		$field_raw_key = str_replace('field_', '', $field['key']);
	?>
	<div class="acf-field acf-field-<?php echo $field_raw_key; ?> acf-field-post-object <?php echo $field_classes['post'] ?>" data-name="<?php echo $field['_name']; ?>[post_id]" data-type="post_object" data-key="<?php echo $field['key']; ?>" style="<?php echo $field_width_style ?>">
		<div class="acf-label">
			<label style="font-weight: normal;" >Internal Link</label>
		</div>
		<div class="acf-input">
		<?php
			// $types = array('post', 'page', 'attachment');
			$types = get_post_types();

			@do_action('acf/render_field/type=post_object', array(
				'name' => $field_name . '[post_id]',
				'value' => $field['value']['post_id'],
				'id' => $field['id'] . '[post_id]',
				'post_type' => $types,
				'allow_null' => 1,
				// '_name' => 'acf[' . $field['_name'] . '][post_id]',
				// 'key' => 'acf[' . $field['key'] . '][post_id]',
			));
		?>
		</div>
	</div>
<div class="acf-field-email <?php echo $field_classes['email'] ?>" style="<?php echo $field_width_style ?>">
	<div class="acf-label">
		<label style="font-weight: normal;">Email Address</label>
	</div>
	<?php

		do_action('acf/render_field/type=email', array(
			'type' => 'email',
			'name' => $field_name . '[email]',
			'value' => $field['value']['email'],
			'label' => __("Email",'acf'),
			'id' => $field_name.'[email]',
			'prepend' => $field['prepend'],
			'append' => $field['append'],
			'class' => '',
			'placeholder' => ''
		));?>

		</div>
		<?php
	return $field;
}


	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/

	function render_field( $field ) {
		$field['value']['text'] = isset($field['value']['text']) ? $field['value']['text'] : $field['default_text'];
		$field['value']['link_type'] = isset($field['value']['link_type']) ? $field['value']['link_type'] : $field['default_link_type'];
		$field['value']['external_url'] = isset($field['value']['external_url']) ? $field['value']['external_url'] : null;
		$field['value']['email'] = isset($field['value']['email']) ? $field['value']['email'] : null;
		$field['value']['post_id'] = isset($field['value']['post_id']) ? $field['value']['post_id'] : null;
		$field['prepend'] = isset($field['prepend']) ? $field['prepend'] : '';
		$field['append'] = isset($field['append']) ? $field['append'] : '';

		/*
		*  Review the data of $field.
		*  This will show what data is available
		*/

		// echo '<pre>';
		// 	print_r( $field );
		// echo '</pre>';

		echo $this->html_text($field);
		echo '<div  style="display: flex; flex-flow: row wrap;">';
		if ( $this->html_link_choices($field ) ) {
			echo $this->html_link_choices($field);
			$field_width_style = 'flex: 1 0 20em;';
		} else {
			$field_width_style = 'flex: 1 0 auto;';
		}
		$this->html_link_fields($field, $field_width_style);
		echo '</div>';
	}


	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/


	function input_admin_enqueue_scripts() {

		// vars
		$url = $this->settings['url'];
		$version = $this->settings['version'];


		// register & include JS
		wp_register_script( 'acf-input-flexible_link', "{$url}assets/js/input.js", array('acf-input'), $version );
		wp_enqueue_script('acf-input-flexible_link');


		// register & include CSS
		// wp_register_style( 'acf-input-flexible_link', "{$url}assets/css/input.css", array('acf-input'), $version );
		// wp_enqueue_style('acf-input-flexible_link');

	}



	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*

	function input_admin_head() {



	}

	*/


	/*
   	*  input_form_data()
   	*
   	*  This function is called once on the 'input' page between the head and footer
   	*  There are 2 situations where ACF did not load during the 'acf/input_admin_enqueue_scripts' and
   	*  'acf/input_admin_head' actions because ACF did not know it was going to be used. These situations are
   	*  seen on comments / user edit forms on the front end. This function will always be called, and includes
   	*  $args that related to the current screen such as $args['post_id']
   	*
   	*  @type	function
   	*  @date	6/03/2014
   	*  @since	5.0.0
   	*
   	*  @param	$args (array)
   	*  @return	n/a
   	*/

   	/*

   	function input_form_data( $args ) {



   	}

   	*/


	/*
	*  input_admin_footer()
	*
	*  This action is called in the admin_footer action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_footer)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*

	function input_admin_footer() {



	}

	*/


	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add CSS + JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*

	function field_group_admin_enqueue_scripts() {

	}

	*/


	/*
	*  field_group_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is edited.
	*  Use this action to add CSS and JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*

	function field_group_admin_head() {

	}

	*/


	/*
	*  load_value()
	*
	*  This filter is applied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/


	// function load_value( $value, $post_id, $field ) {
	// 	// $value['url'] = $value['external_url'];
	// 	return $value;
	//
	// }


	/*
	*  update_value()
	*
	*  This filter is applied to the $value before it is saved in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/

	/*

	function update_value( $value, $post_id, $field ) {

		return $value;

	}

	*/


	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/


	function format_value( $value, $post_id, $field ) {

		// bail early if no value
		if( empty($value)  || 'string' === gettype( $value) ) {

			return $value;

		}

		$url = '';

		switch ($value['link_type']) {
			case 'url':
				$url = $value['external_url'];
				break;
			case 'email':
				$url = 'mailto:' . $value['email'];
				break;
			case 'post':
				$url = get_permalink($value['post_id']);
				break;
		}
		
		if ( ! array_key_exists( 'text', $value ) ) {
			$value['text'] = '';
		}

		if ( array_key_exists( 'text', $value ) ) {
			$link_text = $value['text'];
		} else {
			$link_text = '';
		}
		$link_object = array(
			'text' => $link_text,
			'url' => $url,
			'link_type' => $value['link_type']
		);

		if (!$field['show_text']) {
			$link_object['text'] = false;
		}

		if ( $field['return_type'] === 1 ) {
			return $link_object['url'];
		}


		return $link_object;
	}



	/*
	*  validate_value()
	*
	*  This filter is used to perform validation on the value prior to saving.
	*  All values are validated regardless of the field's required setting. This allows you to validate and return
	*  messages to the user if the value is not correct
	*
	*  @type	filter
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$valid (boolean) validation status based on the value and the field's required setting
	*  @param	$value (mixed) the $_POST value
	*  @param	$field (array) the field array holding all the field options
	*  @param	$input (string) the corresponding input name for $_POST value
	*  @return	$valid
	*/

	/*

	function validate_value( $valid, $value, $field, $input ){

		// Basic usage
		if( $value < $field['custom_minimum_setting'] )
		{
			$valid = false;
		}


		// Advanced usage
		if( $value < $field['custom_minimum_setting'] )
		{
			$valid = __('The value is too little!','acf-flexible-link'),
		}


		// return
		return $valid;

	}

	*/


	/*
	*  delete_value()
	*
	*  This action is fired after a value has been deleted from the db.
	*  Please note that saving a blank value is treated as an update, not a delete
	*
	*  @type	action
	*  @date	6/03/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (mixed) the $post_id from which the value was deleted
	*  @param	$key (string) the $meta_key which the value was deleted
	*  @return	n/a
	*/

	/*

	function delete_value( $post_id, $key ) {



	}

	*/


	/*
	*  load_field()
	*
	*  This filter is applied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/

	/*

	function load_field( $field ) {

		return $field;

	}

	*/


	/*
	*  update_field()
	*
	*  This filter is applied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/

	/*

	function update_field( $field ) {

		return $field;

	}

	*/


	/*
	*  delete_field()
	*
	*  This action is fired after a field is deleted from the database
	*
	*  @type	action
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	n/a
	*/

	/*

	function delete_field( $field ) {



	}

	*/


}


// initialize
new acf_field_flexible_link( $this->settings );


// class_exists check
endif;

?>
