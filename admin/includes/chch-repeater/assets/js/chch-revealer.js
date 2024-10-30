(function ( $ ) {
	var custom_uploader;
	var chchRepeater = {
		// fields actions bind
		currentCount : 0,

		//initial function, makes it alive when document is ready
		init : function () {
			chchRepeater.addRow();
			chchRepeater.deleteRow();
			chchRepeater.revealFields();
			chchRepeater.uniqFields();
		},
		addRow : function () {
			$( ".chch-repeater-add-row" ).on( 'click', function ( e ) {
				e.preventDefault();
				var el = $( this );

				var fieldIndex = el.attr( 'data-row-count' );
				var repeaterId = el.attr( 'data-id' );
				var currentIndex = (parseInt( fieldIndex ) + 1);
				el.attr( 'data-row-count', currentIndex );

				var wrapper = el.siblings( '.chch-repeater-row-wrapper' );

				var fields = wrapper.children( '.chch-repeater-row:first-child' ).clone( true );

				var fields_inputs = fields.find( '.chch-repeater-field, .chch-repeater-row-wrapper' );
				chchRepeater.clearRow( fields_inputs, currentIndex, repeaterId );

				fields.find( '.delete-email-field' ).removeClass( 'hide-section' );

				fields.appendTo( wrapper );
				fields.find( '.chch-repeater-revealer' ).trigger( 'change' );

				chchRepeater.countFields( wrapper );
			} );

		},
		clearRow : function ( fields_inputs, currentIndex, repeaterId ) {
			var pattern = '\\[\\d](.*)$';
			var regex = new RegExp( repeaterId.replace( /\[/g, '\\[' ) + pattern, 'i' );
			fields_inputs.each( function () {
				var input_type = $( this ).attr( 'type' );

				if (input_type == 'text') {
					$( this ).val( '' );
				}

				if (input_type == 'checkbox' || input_type == 'radio') {
					$( this ).prop( 'checked', false );
				}

				if (input_type == 'radio' && $( this ).attr( 'data-group_rows' ) == 'true') {
					$( this ).val( currentIndex );
				}

				if ($( this ).is( "select" )) {
					$( this ).children( "option:first-child" ).prop( "selected", true );
				}
				var newId;
				if ($( this ).hasClass( 'chch-repeater-row-wrapper' )) {
					$( this ).children().not( ':first-child' ).remove();
					$( this ).siblings( '.chch-repeater-add-row' ).attr( 'data-row-count', 0 );
					newId = $( this ).siblings( '.chch-repeater-add-row' ).attr( 'data-id' ).replace( regex, repeaterId + "[" + currentIndex + "]$1" );

					$( this ).siblings( '.chch-repeater-add-row' ).attr( 'data-id', newId );
					chchRepeater.clearRow( $( this ).find( '.chch-repeater-field' ), currentIndex, repeaterId );
					chchRepeater.clearRow( $( this ).find( '.chch-repeater-field' ), 0, newId );
				}

				var field_name = $( this ).attr( 'name' );
				if (field_name) {
					var newName = $( this ).attr( 'name' ).replace( regex, repeaterId + "[" + currentIndex + "]$1" );
					$( this ).attr( 'name', newName );
				}
			} );
		},
		deleteRow : function () {
			$( ".chch-repeater-delete-row" ).on( 'click', function ( e ) {
				e.preventDefault();
				var wrapper = $( this ).closest( '.chch-repeater-row-wrapper' );
				$( this ).closest( '.chch-repeater-row' ).not( '.chch-repeater-row:first-child' ).remove();
				chchRepeater.countFields( wrapper );
			} );
		},
		countFields : function ( wrapper ) {
			wrapper.children().children( '.field-count' ).each( function ( index ) {
				$( this ).html( index + 1 );
			} );
		},
		revealFields : function () {
			$( '.chch-repeater' ).on( 'change', '.chch-repeater-revealer', function () {
				$( this ).parent().siblings( ':not([data-show-if*="any"])' ).hide();
				$( this ).parent().siblings( '[data-show-if*="' + $( this ).children( 'option:selected' ).val() + '"]' ).show();
			} );
			$( '.chch-repeater-revealer' ).trigger( 'change' );
		},

		uniqFields : function () {
			$( "[data-uniq='true']" ).on( 'change', function () {

				var uniqId = $( this ).data( 'id' );
				$( "[data-id='" + uniqId + "']" ).prop( 'checked', false );
				$( this ).prop( 'checked', true );
			} );
		}
	};

	$( document ).ready( chchRepeater.init );
})( jQuery );