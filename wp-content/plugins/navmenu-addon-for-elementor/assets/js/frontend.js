/* global screenReaderText */
/**
 * Theme functions file.
 *
 * Contains handlers for navigation and widget area.
 */

(function ($, root, undefined) {

	$( document ).ready(
		function() {

			'use strict';

			var touch       = $( '#touch-menu' );
			var overlaymenu = $( '.overlay-navigation' );

			$( touch ).on(
				'click', function(e) {
					e.preventDefault();
					overlaymenu.toggleClass( "visible" );
					$( 'body' ).toggleClass( "menu-open" );
					touch.toggleClass( "on" );
				}
			);

			$( window ).resize(
				function(){
					var w = $( window ).width();
					if (w > 768 && overlaymenu.is( ':hidden' )) {
						overlaymenu.removeAttr( 'style' );
					}
				}
			);

			function fullWindow() {
				$( ".fullwindow" ).css( "height", $( window ).height() );
			};
			fullWindow();

			$( window ).resize(
				function() {
					fullWindow();
				}
			);

		}
	);

})( jQuery );


( function( $ ) {
	var body, menuAreaPrimary, elmenuTogglePrimary, elementorPrimaryNavigation, elementorPrimaryHeaderMenu, resizeTimer;

	function initElementorPrimaryNavigation( container ) {

		// Add dropdown toggle that displays child menu items.
		var eldropdownToggle = $(
			'<button />', {
				'class': 'eldropdown-toggle',
				'aria-expanded': false
			}
		).append(
			$(
				'<span />', {
					'class': 'screen-reader-text',
					text: elementorScreenReaderText.expand
					}
			)
		);

		container.find( '.menu-item-has-children > a' ).after( eldropdownToggle );

		// Toggle buttons and submenu items with active children menu items.
		container.find( '.current-menu-ancestor > button' ).addClass( 'eltoggled-on' );
		container.find( '.current-menu-ancestor > .sub-menu' ).addClass( 'eltoggled-on' );

		// Add menu items with submenus to aria-haspopup="true".
		container.find( '.menu-item-has-children' ).attr( 'aria-haspopup', 'true' );

		container.find( '.eldropdown-toggle' ).click(
			function( e ) {
					var _this        = $( this ),
					screenReaderSpan = _this.find( '.screen-reader-text' );

					e.preventDefault();
					_this.toggleClass( 'eltoggled-on' );
					_this.next( '.children, .sub-menu' ).toggleClass( 'eltoggled-on' );

					// jscs:disable
					_this.attr( 'aria-expanded', _this.attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
					// jscs:enable
					screenReaderSpan.text( screenReaderSpan.text() === elementorScreenReaderText.expand ? elementorScreenReaderText.collapse : elementorScreenReaderText.expand );
			}
		);
	}
	initElementorPrimaryNavigation( $( '.elementor-navigation' ) );

	menuAreaPrimary            = $( '#elementor-header-primary' );
	elmenuTogglePrimary        = menuAreaPrimary.find( '#elementor-menu-toggle' );
	elementorPrimaryHeaderMenu = menuAreaPrimary.find( '#elementor-menu' );
	elementorPrimaryNavigation = menuAreaPrimary.find( '#elementor-navigation' );

	// Enable elmenuTogglePrimary.
	( function() {

		// Return early if elmenuTogglePrimary is missing.
		if ( ! elmenuTogglePrimary.length ) {
			return;
		}

		// Add an initial values for the attribute.
		elmenuTogglePrimary.add( elementorPrimaryNavigation ).attr( 'aria-expanded', 'false' );

		elmenuTogglePrimary.on(
			'click.actions', function() {
				$( this ).add( elementorPrimaryHeaderMenu ).toggleClass( 'eltoggled-on' );

				// jscs:disable
				$( this ).add( elementorPrimaryNavigation ).attr( 'aria-expanded', $( this ).add( elementorPrimaryNavigation ).attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
				// jscs:enable
			}
		);
	} )();

	// Fix sub-menus for touch devices and better focus for hidden submenu items for accessibility.
	( function() {
		if ( ! elementorPrimaryNavigation.length || ! elementorPrimaryNavigation.children().length ) {
			return;
		}

		// Toggle `focus` class to allow submenu access on tablets.
		function toggleFocusClassTouchScreen() {
			if ( window.innerWidth >= 910 ) {
				$( document.body ).on(
					'touchstart.actions', function( e ) {
						if ( ! $( e.target ).closest( '.elementor-navigation li' ).length ) {
							$( '.elementor-navigation li' ).removeClass( 'focus' );
						}
					}
				);
				elementorPrimaryNavigation.find( '.menu-item-has-children > a' ).on(
					'touchstart.actions', function( e ) {
						var el = $( this ).parent( 'li' );

						if ( ! el.hasClass( 'focus' ) ) {
							e.preventDefault();
							el.toggleClass( 'focus' );
							el.siblings( '.focus' ).removeClass( 'focus' );
						}
					}
				);
			} else {
				elementorPrimaryNavigation.find( '.menu-item-has-children > a' ).unbind( 'touchstart.actions' );
			}
		}

		if ( 'ontouchstart' in window ) {
			$( window ).on( 'resize.actions', toggleFocusClassTouchScreen );
			toggleFocusClassTouchScreen();
		}

		elementorPrimaryNavigation.find( 'a' ).on(
			'focus.actions blur.actions', function() {
				$( this ).parents( '.menu-item' ).toggleClass( 'focus' );
			}
		);
	} )();

	// Add the default ARIA attributes for the menu toggle and the navigations.
	function onResizeARIA() {
		if ( window.innerWidth < 910 ) {
			if ( elmenuTogglePrimary.hasClass( 'eltoggled-on' ) ) {
				elmenuTogglePrimary.attr( 'aria-expanded', 'true' );
			} else {
				elmenuTogglePrimary.attr( 'aria-expanded', 'false' );
			}

			if ( elementorPrimaryHeaderMenu.hasClass( 'eltoggled-on' ) ) {
				elementorPrimaryNavigation.attr( 'aria-expanded', 'true' );
			} else {
				elementorPrimaryNavigation.attr( 'aria-expanded', 'false' );
			}

			elmenuTogglePrimary.attr( 'aria-controls', 'site-navigation' );
		} else {
			elmenuTogglePrimary.removeAttr( 'aria-expanded' );
			elementorPrimaryNavigation.removeAttr( 'aria-expanded' );
			elmenuTogglePrimary.removeAttr( 'aria-controls' );
		}
	}

	// Start our Secondary navigation
	( function( $ ) {
		var body, menuAreaSecondary, elmenuToggleSecondary, elementorSecondaryNavigation, elementorSecondaryHeaderMenu, resizeTimer;

		function initElementorSecondaryNavigation( container ) {

			// Add dropdown toggle that displays child menu items.
			var eldropdownSecondaryToggle = $(
				'<button />', {
					'class': 'eldropdown-toggle',
					'aria-expanded': false
				}
			).append(
				$(
					'<span />', {
						'class': 'screen-reader-text',
						text: elementorSecondaryScreenReaderText.expand
						}
				)
			);

			container.find( '#elementor-header-secondary .menu-item-has-children > a' ).after( eldropdownSecondaryToggle );

			// Toggle buttons and submenu items with active children menu items.
			container.find( '#elementor-header-secondary .current-menu-ancestor > button' ).addClass( 'eltoggled-on' );
			container.find( '#elementor-header-secondary .current-menu-ancestor > .sub-menu' ).addClass( 'eltoggled-on' );

			// Add menu items with submenus to aria-haspopup="true".
			container.find( '#elementor-header-secondary .menu-item-has-children' ).attr( 'aria-haspopup', 'true' );

			container.find( '#elementor-header-secondary .eldropdown-toggle' ).click(
				function( e ) {
						var _this        = $( this ),
						screenReaderSpan = _this.find( '.screen-reader-text' );

						e.preventDefault();
						_this.toggleClass( 'eltoggled-on' );
						_this.next( '.children, .sub-menu' ).toggleClass( 'eltoggled-on' );

						// jscs:disable
						_this.attr( 'aria-expanded', _this.attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
						// jscs:enable
						screenReaderSpan.text( screenReaderSpan.text() === elementorSecondaryScreenReaderText.expand ? elementorSecondaryScreenReaderText.collapse : elementorSecondaryScreenReaderText.expand );
				}
			);
		}
		initElementorSecondaryNavigation( $( '.elementor-navigation' ) );

		menuAreaSecondary            = $( '#elementor-header-secondary' );
		elmenuToggleSecondary        = menuAreaSecondary.find( '#elementor-menu-toggle' );
		elementorSecondaryHeaderMenu = menuAreaSecondary.find( '#elementor-menu' );
		elementorSecondaryNavigation = menuAreaSecondary.find( '#elementor-navigation' );

		// Enable elmenuToggleSecondary.
		( function() {

			// Return early if elmenuToggleSecondary is missing.
			if ( ! elmenuToggleSecondary.length ) {
				return;
			}

			// Add an initial values for the attribute.
			elmenuToggleSecondary.add( elementorSecondaryNavigation ).attr( 'aria-expanded', 'false' );

			elmenuToggleSecondary.on(
				'click.actions', function() {
					$( this ).add( elementorSecondaryHeaderMenu ).toggleClass( 'eltoggled-on' );

					// jscs:disable
					$( this ).add( elementorSecondaryNavigation ).attr( 'aria-expanded', $( this ).add( elementorSecondaryNavigation ).attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
					// jscs:enable
				}
			);
		} )();

		// Fix sub-menus for touch devices and better focus for hidden submenu items for accessibility.
		( function() {
			if ( ! elementorSecondaryNavigation.length || ! elementorSecondaryNavigation.children().length ) {
				return;
			}

			// Toggle `focus` class to allow submenu access on tablets.
			function toggleFocusClassTouchScreen() {
				if ( window.innerWidth >= 910 ) {
					$( document.body ).on(
						'touchstart.actions', function( e ) {
							if ( ! $( e.target ).closest( '.elementor-navigation li' ).length ) {
								$( '.elementor-navigation li' ).removeClass( 'focus' );
							}
						}
					);
					elementorSecondaryNavigation.find( '.menu-item-has-children > a' ).on(
						'touchstart.actions', function( e ) {
							var el = $( this ).parent( 'li' );

							if ( ! el.hasClass( 'focus' ) ) {
								e.preventDefault();
								el.toggleClass( 'focus' );
								el.siblings( '.focus' ).removeClass( 'focus' );
							}
						}
					);
				} else {
					elementorSecondaryNavigation.find( '.menu-item-has-children > a' ).unbind( 'touchstart.actions' );
				}
			}

			if ( 'ontouchstart' in window ) {
				$( window ).on( 'resize.actions', toggleFocusClassTouchScreen );
				toggleFocusClassTouchScreen();
			}

			elementorSecondaryNavigation.find( 'a' ).on(
				'focus.actions blur.actions', function() {
					$( this ).parents( '.menu-item' ).toggleClass( 'focus' );
				}
			);
		} )();

		// Add the default ARIA attributes for the menu toggle and the navigations.
		function onResizeARIA() {
			if ( window.innerWidth < 910 ) {
				if ( elmenuToggleSecondary.hasClass( 'eltoggled-on' ) ) {
					elmenuToggleSecondary.attr( 'aria-expanded', 'true' );
				} else {
					elmenuToggleSecondary.attr( 'aria-expanded', 'false' );
				}

				if ( elementorSecondaryHeaderMenu.hasClass( 'eltoggled-on' ) ) {
					elementorSecondaryNavigation.attr( 'aria-expanded', 'true' );
				} else {
					elementorSecondaryNavigation.attr( 'aria-expanded', 'false' );
				}

				elmenuToggleSecondary.attr( 'aria-controls', 'site-navigation' );
			} else {
				elmenuToggleSecondary.removeAttr( 'aria-expanded' );
				elementorSecondaryNavigation.removeAttr( 'aria-expanded' );
				elmenuToggleSecondary.removeAttr( 'aria-controls' );
			}
		}

		// Search slideOut
		function slideOut(parent) {
			$( parent ).each(
				function(){
					var label      = $( this ).find( '.label' ),
					form           = $( this ).find( '.form' ),
					dismiss        = $( this ).find( '.dismiss' ),
					formMarginLeft = form.css( 'margin-left' ),
					spd            = 500;

					label.click(
						function(){
							form.animate( {'margin-left':0},spd )
							label.hide()
							label.animate( {'opacity':0},spd )
							setTimeout(
								function(){
									dismiss.show()
									dismiss.animate( {'opacity':1},spd )
								},spd
							)
						}
					)

					dismiss.click(
						function(){
							form.animate( {'margin-left':formMarginLeft},spd )
							dismiss.hide()
							dismiss.animate( {'opacity':0},spd )
							setTimeout(
								function(){
									label.show()
									label.animate( {'opacity':1},spd )
								},spd
							)
						}
					)
				}
			)
		}

		slideOut( '.search' );
	} )( jQuery );

	(function ($, root, undefined) {

		var document = window.document;
		'use strict';

		function extend( a, b ) {
			for ( var key in b ) {
				if ( b.hasOwnProperty( key ) ) {
					a[key] = b[key];
				}
			}
			return a;
		}

		function cbpHorizontalSlideOutMenu( el, options ) {
			this.el      = el;
			this.options = extend( this.defaults, options );
			this._init();
		}

		cbpHorizontalSlideOutMenu.prototype = {

			defaults : {},
			_init : function() {
				this.current          = -1;
				this.touch            = Modernizr.touch;
				this.menu             = this.el.querySelector( '.cbp-hsmenu' );
				this.menuItems        = this.el.querySelectorAll( '.cbp-hsmenu > li' );
				this.menuBg           = document.createElement( 'div' );
				this.menuBg.className = 'cbp-hsmenubg';
				this.el.appendChild( this.menuBg );
				this._initEvents();
			},
			_openMenu : function( el, ev ) {

				var self     = this,
				item         = el.parentNode,
				items        = Array.prototype.slice.call( this.menuItems ),
				submenu      = item.querySelector( '.cbp-hssubmenu' ),
				closeCurrent = function( current ) {
					var current       = current || self.menuItems[ self.current ];
					current.className = '';
					current.setAttribute( 'data-open', '' );
				},
				closePanel = function() {
					self.current             = -1;
					self.menuBg.style.height = '0px';
				};

				if ( submenu ) {

					ev.preventDefault();

					if ( item.getAttribute( 'data-open' ) === 'open' ) {
						closeCurrent( item );
						closePanel();
					} else {
						item.setAttribute( 'data-open', 'open' );
						if ( self.current !== -1 ) {
							closeCurrent();
						}
						self.current             = items.indexOf( item );
						item.className           = 'cbp-hsitem-open';
						self.menuBg.style.height = submenu.offsetHeight + 'px';
					}
				} else {
					if ( self.current !== -1 ) {
						closeCurrent();
						closePanel();
					}
				}

			},
			_initEvents : function() {

				var self = this;

				Array.prototype.slice.call( this.menuItems ).forEach(
					function( el, i ) {
						var trigger = el.querySelector( 'a' );
						if ( self.touch ) {
							trigger.addEventListener( 'touchstart', function( ev ) { self._openMenu( this, ev ); } );
						} else {
							trigger.addEventListener( 'click', function( ev ) { self._openMenu( this, ev ); } );
						}
					}
				);

				window.addEventListener( 'resize', function( ev ) { self._resizeHandler(); } );

			},
			// taken from https://github.com/desandro/vanilla-masonry/blob/master/masonry.js by David DeSandro
			// original debounce by John Hann
			// http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
			_resizeHandler : function() {
				var self = this;
				function delayed() {
					self._resize();
					self._resizeTimeout = null;
				}

				if ( this._resizeTimeout ) {
					clearTimeout( this._resizeTimeout );
				}

				this._resizeTimeout = setTimeout( delayed, 50 );
			},
			_resize : function() {
				if ( this.current !== -1 ) {
					this.menuBg.style.height = this.menuItems[ this.current ].querySelector( '.cbp-hssubmenu' ).offsetHeight + 'px';
				}
			}
		}

		// add to global namespace
		window.cbpHorizontalSlideOutMenu = cbpHorizontalSlideOutMenu;

		var menu = new cbpHorizontalSlideOutMenu( document.getElementById( 'cbp-hsmenu-wrapper' ) );

	})( jQuery, this.el );

} )( jQuery );

jQuery( document ).ready(
	function() {
		jQuery( "#cbp-hsmenu-wrapper ul.sub-menu" ).each(
			function(i){
				jQuery( this ).removeClass( "sub-menu" );
				jQuery( this ).addClass( "cbp-hssubmenu" );
			}
		);
	}
);
