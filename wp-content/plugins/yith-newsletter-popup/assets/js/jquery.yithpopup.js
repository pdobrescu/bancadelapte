/**
 * jQuery Yith Popup Plugin v1.0.0
 *
 * @author Your Inspiration Themes
 * @package YITH Newsletter Popup
 * @version 1.0.0
 *
 */
(function($) {
    "use strict";

    $.yith_popup = function(element, options) {

        var defaults = {
            'popup_class' : 'yith-popup',
            'content' : ''
        };

        var self = this;

        self.settings = {};

        var $element = $(element),
            element = element,
            overlay = null,
            popup = null,
            close = null;

        self.init = function() {
            self.settings = $.extend({}, defaults, options);

            _createElements();
            _initEvents();

        };

        var _initEvents = function() {
                $(document).on('touchstart click', '.yithpopup_overlay', function(e){
                    if( $( e.target).hasClass('close') || $( e.target ).parents( '.yithpopup_overlay' ).length == 0 ) {
                        _close();
                    }
                }).on('keyup', function(e) {
                        if (e.keyCode == 27) {
                            _close();
                        }
                    }).on('click', '.yithpopup_wrapper a.close', function () {
                        _close();
                    });

                $(window).on('resize', function(){
                    _center();
                });

                $('html').removeClass('yith-opened');

                _open();
            },

            _createElements = function() {
                if( $('body').find('.yithpopup_overlay').length == 0 ) {
                    self.overlay = $('<div />', {
                        'class' : 'yithpopup_overlay'
                    }).appendTo('body');
                } else {
                    self.overlay = $('body').find('.yithpopup_overlay');
                }

                if( self.overlay.find('.yithpopup_wrapper').length == 0 ) {
                    self.popup = $('<div />', {
                        'class' : 'yithpopup_wrapper ' + self.settings.popup_class
                    }).appendTo( $('body') );
                } else {
                    self.popup = $('body').find('.yithpopup_wrapper');
                }

                if( self.overlay.find('.close').length == 0 ) {
                    self.close = $('<a />', {
                        'class' : 'close'
                    }).appendTo( self.popup );
                } else {
                    self.close = self.overlay.find('.close');
                }
            },

            _center = function() {
                self.popup.css({
                    position: 'fixed',
                    top: Math.max(0, ((jQuery(window).height() - self.popup.outerHeight()) / 2) ) + "px",//'15%',
                    left: Math.max(0, ((jQuery(window).width() - self.popup.outerWidth()) / 2) ) + "px"
                });
            },

            _open = function() {
                _center();
                _content();
                self.overlay.css({ 'display': 'block', opacity: 0 }).animate({ opacity: 1 }, 500);
                $('html').addClass('yith-opened');
            },

            _close = function() {
                self.overlay.css({ 'display': 'none', opacity: 1 }).animate({ opacity: 0 }, 500);
                $element.trigger('close.yith-popup');
                $('html').removeClass('yith-opened');
                _destroy();
            },

            _destroy = function() {
                self.popup.remove();
                self.overlay.remove();

                //self.popup = self.overlay = null;
                $element.removeData('yith_popup');
            },

            _content = function() {
                if( self.settings.content != '' ) {
                    self.popup.html( self.settings.content );
                } else if( $element.data('container') ) {
                    self.popup.html( $($element.data('container')).html() );
                } else if( $element.data('content') ) {
                    self.popup.html( $element.data('content') );
                } else if( $element.attr('title') ) {
                    self.popup.html( $element.attr('title') );
                } else {
                    self.popup.html('');
                }

                //update <input id="" /> and <label for="">
                self.popup.find('form, input, label, a').each(function(){
                    if( typeof $(this).attr('id') != 'undefined' ) {
                        var id = $(this).attr('id') + '_yith-popup';
                        $(this).attr('id', id);
                    }

                    if( typeof $(this).attr('for') != 'undefined' ) {
                        var id = $(this).attr('for') + '_yith-popup';
                        $(this).attr('for', id);
                    }
                });

                if( self.overlay.find('.close').length == 0 ) {
                    self.close = $('<a />', {
                        'class' : 'close'
                    }).appendTo( self.popup );
                } else {
                    self.close = self.overlay.find('.close');
                }
            };

        self.init();
    };

    $.fn.yith_popup = function(options) {

        return this.each(function() {
            if (undefined === $(this).data('yith_popup')) {
                var yith_popup = new $.yith_popup(this, options);
                $(this).data('yith_popup', yith_popup);
            }
        });

    };

})(jQuery);
