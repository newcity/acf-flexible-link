(function($){


	function initialize_field( $el ) {
		console.log('Field initializing');
		// define vars
		var $radio = $el.find('.link_type_picker'),
			$internal = $el.find('.acf-field-post-object'),
			$external = $el.find('.acf-field-url'),
			$email = $el.find('.acf-field-email');

		// listen to radio button change
		$radio.change(function() {
	        helperRadioChange(this, $internal, $external, $email);
	    });

		// trigger change function on init to respect current state (do not trigger change event as this provokes browser alert on window close)
		// helperRadioChange($radio, $internal, $external, $email);

	}

	function helperRadioChange(_self, $internal, $external, $email) {
		hiddenClass = 'acf-hidden';

		console.log('radio button changed');

		radioValue = $(_self).find('input').val();


		$internal.removeClass(hiddenClass);
		$external.removeClass(hiddenClass);
		$email.removeClass(hiddenClass);

		// if ($(_self).is(':checked')) {
		console.log(radioValue);
			switch (radioValue) {
				case 'post':
					$external.addClass(hiddenClass);
					$email.addClass(hiddenClass);
					break;
				case 'url':
					$internal.addClass(hiddenClass);
					$email.addClass(hiddenClass);
					break;
				case 'email':
					$internal.addClass(hiddenClass);
					$external.addClass(hiddenClass);
					break;
			}
		// }
		// if($(_self).is(":checked")) {
    //         $internal.hide();
    //         $external.show();
    //         $external.find('input').show();
    //     } else {
    //     	$internal.show();
    //     	$external.hide();
    //     }
	}

	if( typeof acf.add_action !== 'undefined' ) {

		/*
		*  ready append (ACF5)
		*
		*  These are 2 events which are fired during the page load
		*  ready = on page load similar to $(document).ready()
		*  append = on new DOM elements appended via repeater field
		*
		*  @type	event
		*  @date	20/07/13
		*
		*  @param	$el (jQuery selection) the jQuery element which contains the ACF fields
		*  @return	n/a
		*/

		acf.add_action('ready append', function( $el ){

			// search $el for fields of type 'button'
			acf.get_fields({ type : 'flexible_link'}, $el).each(function(){

				initialize_field( $(this) );

			});

		});


	} else {


		/*
		*  acf/setup_fields (ACF4)
		*
		*  This event is triggered when ACF adds any new elements to the DOM.
		*
		*  @type	function
		*  @since	1.0.0
		*  @date	01/01/12
		*
		*  @param	event		e: an event object. This can be ignored
		*  @param	Element		postbox: An element which contains the new HTML
		*
		*  @return	n/a
		*/

		$(document).on('acf/setup_fields', function(e, postbox){

			$(postbox).find('.field[data-field_type="flexible_link"]').each(function(){

				initialize_field( $(this) );

			});

		});


	}


})(jQuery);
