var $ = jQuery.noConflict();
$( document ).ready(function() {
	$( "#wpcustomcontactform" ).validate({
		rules: {
			firstname: "required",
			lastname: "required",
			email: {
				required: true,
				email: true
			},
			contact_no: {
				required: true,
			}
		},
		messages: {
			firstname: "Please Enter your firstname",
			lastname: "Please Enter your lastname",
			email: "Please Enter a valid email address",
			contact_no: "Please Enter a valid phone number"
		},
		submitHandler: function() {
			var firstname              = $( '#firstname' ).val();
			var lastname               = $( '#lastname' ).val();
			var email                  = $( '#email' ).val();
			var contact_no             = $( '#contact_no' ).val();
			var message                = $( '#message' ).val();
		
			$.ajax({
				type: 'post',
				dataType: 'json',
				url: ajax_url.url,
				data: {
					action: 'wp_custom_contact_form_data',
					firstname: firstname,
					lastname: lastname,
					email: email,
					contact_no: contact_no,
					message: message
				},
				success: function (response ) {
					if ( response.success ) {
						$( '.wp-custom-contact-form-main' ).html( '<span class="wp-custom-contact-form-success-msg">' + response.success + '</span>' );
					} else {
						$( '.wp-custom-contact-form-main' ).html( '<span class="wp-custom-contact-form-error-msg">' + response.error + '</span>' );
					}
				}
			});

			return false;  // block the default submit action
		}
		
	});
});
