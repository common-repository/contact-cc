(function ( $ ) {
	var ChChContactProPublic = {

		init : function () {
			ChChContactProPublic.FormSubscribe();
		},
		FormSubscribe : function () {
			$( ".chch-contact-free__form" ).submit( function ( event ) {
				event.preventDefault();

				var sendButton = $( this ).find( '.chch-contact-free__btn' );
				var formId = sendButton.attr( 'data-id' );

				var inputField = $( this ).find( '.chch-contact-free__input' );
				var radioFields = $( this ).find( '.chch-contact-free__radio' );
				var checkboxFields = $( this ).find( '.chch-contact-free__checkbox' );

				var thanks = $( this ).find( '.chch-contact-free__success' );
				var mainError = $( this ).find( '.chch-contact-free__error-main' );
				var fieldsErrors = $( this ).find( '.chch-contact-free__error' );

				var nonce = $( this ).find( '#_chch_cff_nonce' ).val();

				var subscribeParams = {
					action : "chch_lpf_subscribe",
					nonce : nonce,
					form_id : formId
				};

				$( '.chch-pu-form-control__wrapper' ).removeClass( 'show_error' );
				var fields = {};
				inputField.each( function ( index, element ) {
					var name = $( this ).attr( 'name' );
					if (name in fields == false) {
						fields[ name ] = {};
						fields[ name ][ 'fieldName' ] = name;
						fields[ name ][ 'fieldVal' ] = $( this ).val();
						fields[ name ][ 'fieldType' ] = $( this ).data( 'type' );
						fields[ name ][ 'fieldReq' ] = $( this ).data( 'req' );
					}
				} );

				radioFields.each( function ( index, element ) {
					var name = $( this ).attr( 'name' );

					if (name in fields == false) {
						if ($( this ).is( ":checked" )) {
							fields[ name ] = {};
							fields[ name ][ 'fieldName' ] = name;
							fields[ name ][ 'fieldVal' ] = $( this ).val();
							fields[ name ][ 'fieldType' ] = $( this ).data( 'type' );
							fields[ name ][ 'fieldReq' ] = $( this ).data( 'req' );
						}
					}
				} );

				checkboxFields.each( function ( index, element ) {

					if ($( this ).is( ":checked" )) {
						var name = $( this ).attr( 'name' );
						if (name in fields == false) {
							fields[ name ] = {};
							fields[ name ][ 'fieldVal' ] = [];
							fields[ name ][ 'fieldVal' ].push($( this ).val());
							fields[ name ][ 'fieldName' ] = name;
							fields[ name ][ 'fieldType' ] = $( this ).data( 'type' );
							fields[ name ][ 'fieldReq' ] = $( this ).data( 'req' );
						} else {
							fields[ name ][ 'fieldVal' ].push($( this ).val());
						}

					}
				} );
				subscribeParams.fields = fields;
				sendButton.addClass( 'chch-pu-btn-sending' ).prop( "disabled", true );
				inputField.prop( "disabled", true );

				$.ajax( {
					url : chch_contact_form_ajax_object.ajaxUrl,
					async : true,
					type : "POST",
					data : subscribeParams,
					success : function ( data ) {

						var response = JSON.parse( data );

						var subscribeStatus = response.status;
						console.log( subscribeStatus );
						switch ( subscribeStatus ) {
							case 'ok':
								fieldsErrors.fadeOut();
								mainError.fadeOut();
								thanks.fadeIn();
								break;
							case 'fields_error':
								$.each( response.errors, function ( index, el ) {
									var errorFields = $( ".chch-contact-free__form" ).find( 'input[name="' + el.field_name + '"]' );
									errorFields.closest( '.chch-contact-free__input-group' ).addClass( 'show_error' ).find( '.chch-contact-free__error' ).html( el.error_message );
								} );
								break;

							case 'error':
								thanks.fadeOut();
								mainError.html( response.error ).fadeIn();
								break;

							case 'subscribeError':
								thanks.fadeOut();
								mainError.html( response.message ).fadeIn();
								break;
						}
						sendButton.removeClass( 'chch-pu-btn-sending' ).prop( "disabled", false );
						inputField.prop( "disabled", false );

					}
				} );
				$( '.chch-contact-free__error' ).on( 'click', function () {
					$( this ).closest( '.chch-contact-free__input-group' ).removeClass( 'show_error' );
				} )

			} );
		},
		checkJson : function ( str ) {
			try {
				JSON.parse( str );
			} catch ( e ) {
				return false;
			}
			return true;
		}

	};

	$( document ).ready( ChChContactProPublic.init );
})( jQuery );
