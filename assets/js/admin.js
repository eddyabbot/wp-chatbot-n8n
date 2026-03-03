/* global jQuery */
( function ( $ ) {
	'use strict';

	$( document ).ready( function () {

		// --- Color pickers ---
		$( '.wpcn-color-picker' ).wpColorPicker();

		// --- Tab switching ---
		var $tabs   = $( '.wpcn-tabs .nav-tab' );
		var $panels = $( '.wpcn-tab-panel' );

		function switchTab( tabId ) {
			$tabs.removeClass( 'nav-tab-active' );
			$tabs.filter( '[data-tab="' + tabId + '"]' ).addClass( 'nav-tab-active' );

			$panels.removeClass( 'is-active' );
			$( '#wpcn-panel-' + tabId ).addClass( 'is-active' );

			try {
				localStorage.setItem( 'wpcn_active_tab', tabId );
			} catch ( e ) { /* localStorage non disponibile */ }
		}

		$tabs.on( 'click', function ( e ) {
			e.preventDefault();
			switchTab( $( this ).data( 'tab' ) );
		} );

		// Ripristina l'ultimo tab aperto (o il primo)
		var savedTab = '';
		try {
			savedTab = localStorage.getItem( 'wpcn_active_tab' ) || '';
		} catch ( e ) { /* noop */ }

		var firstTab = $tabs.first().data( 'tab' );
		switchTab( savedTab || firstTab );

	} );

} )( jQuery );
