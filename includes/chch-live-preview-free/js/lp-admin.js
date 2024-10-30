(function ($) {
    var custom_uploader;
    var lpf = {
        // fields actions bind
        lpFromActionsBind: false,
        lpFormContainer: $('#chch-lpf-ajax-form-container'),
        //current template
        lpTemplate: 'template',

        //initial function, makes it alive when document is ready
        init: function () {
            lpf.addTabs();
            lpf.templateActivate();
            lpf.templateEdit();
        },


        addTabs: function () {
            var tabs = '<h2 class="nav-tab-wrapper" id="chch-lpf-tabs">' + chch_lpf_ajax_object.chch_lpf_tabs + '</h2>';

            $('#post').prepend(tabs);

            lpf.handleTabs();
        },
        handleTabs: function () {
            $('#chch-lpf-tabs a').on('click', function (e) {
                e.preventDefault();
                var target = $(this).attr('data-target');

                if (!$(this).hasClass('nav-tab-active')) {
                    $( '.chch-lpf-tab' ).removeClass( 'chch-lpf-tab-active' );
                    $( '#chch-lpf-tabs a' ).removeClass( 'nav-tab-active' );
                    $( this ).addClass( 'nav-tab-active' );

                    $( '.' + target ).addClass( 'chch-lpf-tab-active' );
                }
            });
        },
        templateActivate: function () {
            $('.chch-lpf-template-acivate').on('click', function (e) {
                e.preventDefault();

                template = $(this).attr('data-template');

                $('#poststuff .theme-browser .theme.active').removeClass('active');
                theme = $(this).closest('.theme');
                theme.addClass('active');

                $('#_chch_lpf_template').val(template);
                $('#publish').trigger('click');
            });
        },
        templateEdit: function () {
            $('.chch-lpf-template-ajax-edit').on('click', function (e) {
                e.preventDefault();
                thisEl = $(this);
                lpf.lpTemplate = thisEl.attr('data-template');
                post_id = thisEl.attr('data-postid');
                lpf_id = thisEl.attr('data-lpf-id');
                nounce = thisEl.attr('data-nounce');

                $.ajax({
                    url: chch_lpf_ajax_object.ajaxUrl,
                    async: true,
                    type: "POST",
                    data: {
                        action: "chch_lpf_load_lp_form",
                        template: lpf.lpTemplate,
                        nounce: nounce,
                        lpf_id: lpf_id,
                        post_id: post_id

                    },
                    success: function (data) {
                        $('.theme').removeClass('active');
                        thisEl.closest('.theme').addClass('active');
                        $('#_chch_lpf_template').val(lpf.lpTemplate);

                        $('#chch-lpf-ajax-form-container').html(data);
                        lpf.formActionsInit();
                        $('#chch-lpf-ajax-form-container').show();

                        lpf.triggerFields();
                    }
                });
            });
        },
        formActionsInit: function () {
            lpf.customizeStyleFieldsChange();
            lpf.colorPickerFieldInit();
            lpf.sliderFieldInit();
            lpf.removeCheckboxFieldInit();
            lpf.attrFieldInit();
            lpf.contentFieldInit();
            lpf.classSwitcherFieldInit();
            lpf.templateEditClose();
            lpf.accordionHandle();
            lpf.wysiwygFieldInit();
            lpf.revealerFieldInit();
            lpf.uploadFieldInit();
        },
        templateEditClose: function () {
            $('#chch-lpf-ajax-form-container').find('.chch-lpf-customize-close').on('click', function (e) {
                e.preventDefault();

                $('#chch-lpf-customize-form-' + lpf.lpTemplate).hide();
            });
        },
        accordionHandle: function () {
            $('#chch-lpf-ajax-form-container').find(".accordion-section-title").on('click', function (e) {
                el = $(this);
                target = el.next('.accordion-section-content');
                if (!$(this).hasClass('open')) {
                    el.addClass('open');
                    target.slideDown('fast');
                } else {
                    el.removeClass('open');
                    target.slideUp('fast');
                }
            });
        },
        triggerFields: function () {
            $('#chch-lpf-ajax-form-container').find('.chch-lpf-to-trigger').trigger('change');
        },
        colorPickerFieldInit: function () {

            $('#chch-lpf-ajax-form-container').find('.chch-lpf-colorpicker-disabled').wpColorPicker();

            $('#chch-lpf-ajax-form-container').find('.chch-lpf-colorpicker').wpColorPicker({
                change: _.throttle(function () {
                    var el = $(this);
                    lpf.customizeStyle(el.attr('data-customize-target'),
                        el.attr('data-attr'),
                        el.val());
                })
            });

        },
        sliderFieldInit : function () {
            $( '#chch-lpf-ajax-form-container' ).find( ".chch-lpf-slider" ).on( "slide", function ( event, ui ) {
                target = $( this ).attr( "data-target" );
                $( "#" + target ).val( ui.value );
                $( "#" + target ).trigger( "change" );
            } );
        },
        removeCheckboxFieldInit: function () {
            $('#chch-lpf-ajax-form-container').find('.remover-checkbox').on('change', function () {
                var target = $(this).attr('data-customize-target');

                if ($(this).is(':checked')) {
                    $(target).hide();
                } else {
                    $(target).show();
                }
            });
        },
        uploadFieldInit: function () {
            $('#chch-lpf-ajax-form-container').find('.chch-lpf-upload').click(function (e) {

                e.preventDefault();
                target = $(this).attr('data-target');

                //If the uploader object has already been created, reopen the dialog
                if (custom_uploader) {
                    custom_uploader.open();
                    return;
                }

                //Extend the wp.media object
                custom_uploader = wp.media.frames.file_frame = wp.media({
                    title: 'Choose Image',
                    button: {
                        text: 'Choose Image'
                    },
                    multiple: false
                });

                // When a file is selected, grab the URL and set it as the text field's value
                custom_uploader.on('select', function () {
                    attachment = custom_uploader.state().get('selection').first().toJSON();

                    $('#' + target).val(attachment.url);
                    $('#' + target).trigger('change');
                });

                //Open the uploader dialog
                custom_uploader.open();

            });
        },
        attrFieldInit: function () {
            $('#chch-lpf-ajax-form-container').find('.chch-lpf-attr').on("change keyup", function (e) {

                var el = $(this);

                $('#chch-lpf-customize-preview-' + lpf.lpTemplate + ' ' + el.attr('data-customize-target')).attr(el.attr('data-attr'),
                    el.val());

            });
        },

        contentFieldInit: function () {
            $('#chch-lpf-ajax-form-container').find('.chch-lpf-customize-content').on("change keyup", function (e) {

                var el = $(this);

                $('#chch-lpf-customize-preview-' + lpf.lpTemplate + ' ' + el.attr('data-customize-target')).html(el.val());

            });
        },
        wysiwygFieldInit: function () {

            $('#chch-lpf-ajax-form-container .chch-lpf-editor').each(function () {
                editorId = $(this).attr('id');
                var default_options = {
                    'mode': 'html',
                    'mceInit': {
                        "theme": "modern",
                        "skin": "lightgray",
                        "language": "en",
                        "formats": {
                            "alignleft": [
                                {
                                    "selector": "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
                                    "styles": {
                                        "textAlign": "left"
                                    },
                                    "deep": false,
                                    "remove": "none"
                                },
                                {
                                    "selector": "img,table,dl.wp-caption",
                                    "classes": ["alignleft"],
                                    "deep": false,
                                    "remove": "none"
                                }],
                            "aligncenter": [
                                {
                                    "selector": "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
                                    "styles": {
                                        "textAlign": "center"
                                    },
                                    "deep": false,
                                    "remove": "none"
                                },
                                {
                                    "selector": "img,table,dl.wp-caption",
                                    "classes": ["aligncenter"],
                                    "deep": false,
                                    "remove": "none"
                                }],
                            "alignright": [
                                {
                                    "selector": "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
                                    "styles": {
                                        "textAlign": "right"
                                    },
                                    "deep": false,
                                    "remove": "none"
                                },
                                {
                                    "selector": "img,table,dl.wp-caption",
                                    "classes": ["alignright"],
                                    "deep": false,
                                    "remove": "none"
                                }],
                            "strikethrough": {
                                "inline": "del",
                                "deep": true,
                                "split": true
                            }
                        },
                        "relative_urls": false,
                        "remove_script_host": false,
                        "convert_urls": false,
                        "browser_spellcheck": true,
                        "fix_list_elements": true,
                        "entities": "38,amp,60,lt,62,gt",
                        "entity_encoding": "raw",
                        "keep_styles": false,
                        "paste_webkit_styles": "font-weight font-style color",
                        "preview_styles": "font-family font-size font-weight font-style text-decoration text-transform",
                        "wpeditimage_disable_captions": true,
                        "wpeditimage_html5_captions": false,
                        "plugins": "chch_lp_keyup_event,charmap,hr,media,paste,tabfocus,colorpicker,textcolor,fullscreen,wordpress,wpeditimage,wpgallery,wplink,wpdialogs,wpview,image",
                        "selector": "#" + editorId,
                        "resize": "vertical",
                        "menubar": false,
                        "wpautop": true,
                        "indent": false,
                        "toolbar1": "styleselect,bold,italic,strikethrough,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink, forecolor",
                        "toolbar2": "",
                        "toolbar3": "",
                        "toolbar4": "",
                        "tabfocus_elements": ":prev,:next",
                        "body_class": editorId
                    }
                };
                tinyMCEPreInit.mceInit[editorId] = default_options.mceInit;
                tinymce.init(tinyMCEPreInit.mceInit[editorId]);

            });
        },

        classSwitcherFieldInit: function () {
            $('#chch-lpf-ajax-form-container').find('.chch-lpf-class-switcher').on("change", function (e) {

                el = $(this);
                eltarget = el.attr('data-customize-target');

                elOldVal = el.attr('data-old');
                elval = el.find(":selected").val();

                $('#chch-lpf-customize-preview-' + lpf.lpTemplate + ' ' + eltarget).removeClass(elOldVal);
                $('#chch-lpf-customize-preview-' + lpf.lpTemplate + ' ' + eltarget).addClass(elval);

                el.attr('data-old', elval);

            });
        },
        revealerFieldInit: function () {
            $('#chch-lpf-ajax-form-container').find('.chch-lpf-revealer').on('change', function () {
                var el = $(this);

                var group = el.attr('data-group');
                var target = el.find(":selected").val();

                if (target == 'no') {
                    $('#chch-lpf-revealer-section-' + target).slideDown();
                    if (JSON.parse(el.attr('data-add-css'))) {
                        addStyles = JSON.parse(el.attr('data-add-css'));

                        $.each(addStyles, function (index, elem) {
                            $('#chch-lpf-customize-preview-' + lpf.lpTemplate + ' ' + el.attr('data-customize-target')).css(elem.attr,
                                elem.value);
                        });

                    }
                }

                $('.' + group).slideUp();
                $('#chch-lpf-revealer-section-' + target).slideDown(); 
                $('#chch-lpf-revealer-section-' + target).find('.chch-lpf-customize-style').trigger('change');
            });
        },
        customizeStyleFieldsChange: function () {

            $('#chch-lpf-ajax-form-container').find('.chch-lpf-customize-style').on('change', function (e) {
                el = $(this);
                lpf.customizeStyle(el.attr('data-customize-target'),
                    el.attr('data-attr'),
                    el.val(),
                    el.attr('data-unit'),
                    el.attr('data-add-css'));
            });
        },
        customizeStyle: function (customizeTarget, cssAttr, cssValue, valueUnit, addCss) {
            if (typeof valueUnit !== "undefined") {
                cssValue = cssValue + valueUnit;
            }

            if (typeof addCss !== "undefined") {
                if (JSON.parse(addCss)) {
                    addStyles = JSON.parse(addCss);

                    $.each(addStyles, function (index, el) {
                        $('#chch-lpf-customize-preview-' + lpf.lpTemplate + ' ' + customizeTarget).css(el.attr, el.value);
                    });

                }
            }

            if (cssAttr == 'background-image') {
                $('#chch-lpf-customize-preview-' + lpf.lpTemplate + ' ' + customizeTarget).css('background-image',
                    'url(' + cssValue + ')');
            } else {
                $('#chch-lpf-customize-preview-' + lpf.lpTemplate + ' ' + customizeTarget).css(cssAttr, cssValue);
            }
        }

    };

    $(document).ready(lpf.init);
})(jQuery);