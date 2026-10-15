/**
 * VerQuin theme — site-wide behaviour.
 *
 * One file, one named section per owner, each in its own IIFE (mirrors theme.css).
 * No dependencies. Enqueued deferred by inc/enqueue.php.
 *
 *   Site header (global shell) + menu block
 */

/* --------------------------------------------------------------------------
   Site header: mobile navigation panel and submenu toggles
   Owner: .site-header (global shell), .b-menu (block)
   Desktop hover/focus-within dropdowns stay CSS-driven; this adds the
   button-driven (touch/keyboard) path on every width.
   -------------------------------------------------------------------------- */
( function () {
	'use strict';

	var DESKTOP = window.matchMedia( '(min-width: 1041px)' ); /* --bp-lg */

	var header = document.querySelector( '.site-header' );
	if ( ! header ) {
		return;
	}

	var toggle = header.querySelector( '.site-header__toggle' );
	var panel  = header.querySelector( '.site-header__nav' );
	var items  = header.querySelectorAll( '.b-menu__item--has-sub' );

	/* ---------------------------------------------------------------- Submenus */

	function setSubmenu( item, open ) {
		var button = item.querySelector( ':scope > .b-menu__toggle' );
		item.classList.toggle( 'b-menu__item--open', open );
		if ( button ) {
			button.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		}
	}

	function closeSubmenus( except ) {
		items.forEach( function ( item ) {
			if ( item !== except ) {
				setSubmenu( item, false );
			}
		} );
	}

	items.forEach( function ( item ) {
		var button = item.querySelector( ':scope > .b-menu__toggle' );
		if ( ! button ) {
			return;
		}

		button.addEventListener( 'click', function () {
			var open = ! item.classList.contains( 'b-menu__item--open' );
			closeSubmenus( item );
			setSubmenu( item, open );
		} );

		item.addEventListener( 'keydown', function ( event ) {
			if ( event.key === 'Escape' && item.classList.contains( 'b-menu__item--open' ) ) {
				event.stopPropagation();
				setSubmenu( item, false );
				button.focus();
			}
		} );
	} );

	/* ------------------------------------------------------------ Mobile panel */

	function isOpen() {
		return header.classList.contains( 'site-header--open' );
	}

	function setPanel( open ) {
		if ( ! toggle || ! panel ) {
			return;
		}
		header.classList.toggle( 'site-header--open', open );
		document.body.classList.toggle( 'site-header--nav-open', open );
		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );

		var label = toggle.querySelector( '.screen-reader-text' );
		if ( label ) {
			label.textContent = open ? toggle.dataset.labelClose : toggle.dataset.labelOpen;
		}
		if ( ! open ) {
			closeSubmenus( null );
		}
	}

	if ( toggle && panel ) {
		toggle.addEventListener( 'click', function () {
			setPanel( ! isOpen() );
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( event.key !== 'Escape' ) {
				return;
			}
			if ( isOpen() ) {
				setPanel( false );
				toggle.focus();
			} else {
				closeSubmenus( null );
			}
		} );

		document.addEventListener( 'click', function ( event ) {
			if ( ! header.contains( event.target ) ) {
				setPanel( false );
				closeSubmenus( null );
			}
		} );

		var onDesktopChange = function ( mql ) {
			if ( mql.matches && isOpen() ) {
				setPanel( false );
			}
		};
		if ( typeof DESKTOP.addEventListener === 'function' ) {
			DESKTOP.addEventListener( 'change', onDesktopChange );
		} else {
			DESKTOP.addListener( onDesktopChange );
		}
	}
} )();
