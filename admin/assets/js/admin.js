(function ( $ ) {
	var ChChContactPro = {

		init : function () {
			ChChContactPro.SelectRevealerInit();
			ChChContactPro.CheckboxRevealerInit();
			ChChContactPro.TriggerRevealers();

			$('#wpbody-content > .wrap').prepend('<a class="button button-secondary right button-hero" target="_blank" style="margin: 25px 0px 0px 2px; padding: 0px 20px; height: 47px;" href="https://shop.chop-chop.org/contact" target="_blank">Contact Support</a><a class="button button-primary right button-hero" target="_blank" href="http://ch-ch.org/contactpro" style="margin: 25px 20px 0 2px;">Get Pro</a>');
		},
		SelectRevealerInit : function () {
			$( '[data-select-target]' ).each( function () {
				$( this ).on( 'change', function () {
					var el = $( this );
					var thisOption = el.find( ":selected" );
					var target = thisOption.val();
					var dataSelectTarget = el.attr( 'data-select-target' );
					var dataSelectTrigger = el.attr( 'data-select-trigger' );
					if (typeof dataSelectTrigger !== 'undefined') {
						if (el.val() === dataSelectTrigger) {
							$( dataSelectTarget ).removeClass( 'hide-section' );
						} else {
							$( dataSelectTarget ).addClass( 'hide-section' );
						}
					} else {
						$( dataSelectTarget ).addClass( 'hide-section' );
						$( dataSelectTarget + '.' + target + '-wrapper' ).removeClass( 'hide-section' );
					}
				} );
			} );
		},
		CheckboxRevealerInit : function () {
			$( '[data-checkbox-target]' ).on( 'change', function () {
				var dataCheckboxTarget = $( this ).attr( 'data-checkbox-target' );
				if ($( this ).is( ':checked' )) {
					$( dataCheckboxTarget ).removeClass( 'hide-section' );
				} else {
					$( dataCheckboxTarget ).addClass( 'hide-section' );
				}
			} );
		},
		TriggerRevealers : function () {
			$( '[data-select-target]' ).trigger( 'change' );
			$( '[data-checkbox-target]' ).trigger( 'change' );
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

	$( document ).ready( ChChContactPro.init );
})( jQuery );
