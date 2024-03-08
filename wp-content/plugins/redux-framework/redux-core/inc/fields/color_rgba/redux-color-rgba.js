/* global redux, redux_change */

(function( $ ) {
	'use strict';

	redux.field_objects                    = redux.field_objects || {};
	redux.field_objects.color_rgba         = redux.field_objects.color_rgba || {};
	redux.field_objects.color_rgba.fieldID = '';

	redux.field_objects.color_rgba.hexToRGBA = function( hex, alpha ) {
		var result;
		var r;
		var g;
		var b;

		if ( null === hex ) {
			result = '';
		} else {
			hex = hex.replace( '#', '' );
			r   = parseInt( hex.substring( 0, 2 ), 16 );
			g   = parseInt( hex.substring( 2, 4 ), 16 );
			b   = parseInt( hex.substring( 4, 6 ), 16 );

			result = 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
		}

		return result;
	};

	redux.field_objects.color_rgba.init = function( selector ) {
		selector = $.redux.getSelector( selector, 'color_rgba' );

		$( selector ).each(
			function() {
				var el     = $( this );
				var parent = el;

				if ( ! el.hasClass( 'redux-field-container' ) ) {
					parent = el.parents( '.redux-field-container:first' );
				}

				if ( parent.is( ':hidden' ) ) {
					return;
				}

				if ( parent.hasClass( 'redux-field-init' ) ) {
					parent.removeClass( 'redux-field-init' );
				} else {
					return;
				}

				redux.field_objects.color_rgba.modInit( el );
				redux.field_objects.color_rgba.initColorPicker( el );
			}
		);
	};

	redux.field_objects.color_rgba.modInit = function( el ) {
		redux.field_objects.color_rgba.fieldID = el.find( '.redux-color_rgba-container' ).data( 'id' );
	};

	// Initialize colour picker.
	redux.field_objects.color_rgba.initColorPicker = function( el ) {

		// Get field ID.
		var field_id = redux.field_objects.color_rgba.fieldID;

		// Get the color scheme container.
		var colorpickerInput = el.find( '.redux-color-rgba' );

		// Get alpha value and sanitize it.
		var currentAlpha = colorpickerInput.data( 'current-alpha' );

		// Get colour value and sanitize it.
		var currentColor = colorpickerInput.data( 'current-color' );

		var outputTransparent = colorpickerInput.data( 'output-transparent' );

		// Color picker arguments.
		var container = el.find( '.redux-color-rgba-container' );

		// Get, decode and parse palette.
		var palette = container.data( 'palette' );

		// Get and sanitize show input argument.
		var showInput = container.data( 'show-input' );

		// Get and sanitize show initial argument.
		var showInitial = container.data( 'show-initial' );

		// Get and sanitize show alpha argument.
		var showAlpha = container.data( 'show-alpha' );

		// Get and sanitize allow empty argument.
		var allowEmpty = container.data( 'allow-empty' );

		// Get and sanitize show palette argument.
		var showPalette = container.data( 'show-palette' );

		// Get and sanitize show palette only argument.
		var showPaletteOnly = container.data( 'show-palette-only' );

		// Get and sanitize show selection palette argument.
		var showSelectionPalette = container.data( 'show-selection-palette' );

		// Get max palette size.
		var maxPaletteSize = Number( container.data( 'max-palette-size' ) );

		// Get and sanitize clickout fires change argument.
		var clickoutFiresChange = container.data( 'clickout-fires-change' );

		// Get choose button text.
		var chooseText = String( container.data( 'choose-text' ) );

		// Get cancel button text.
		var cancelText = String( container.data( 'cancel-text' ) );

		// Get cancel button text.
		var inputText = String( container.data( 'input-text' ) );

		// Get and sanitize show buttons argument.
		var showButtons = container.data( 'show-buttons' );

		// Get container class.
		var containerClass = String( container.data( 'container-class' ) );

		// Get replacer class.
		var replacerClass = String( container.data( 'replacer-class' ) );

		currentAlpha      = Number( ( null === currentAlpha || undefined === currentAlpha ) ? 1 : currentAlpha );
		currentColor      = ( '' === currentColor || 'transparent' === currentColor ) ? '' : currentColor;
		outputTransparent = Boolean( ( '' === outputTransparent ) ? false : outputTransparent );

		palette = decodeURIComponent( palette );
		palette = JSON.parse( palette );

		// Default palette.
		if ( null === palette ) {
			palette = [
				['#000000', '#434343', '#666666', '#999999', '#b7b7b7', '#cccccc', '#d9d9d9', '#efefef', '#f3f3f3', '#ffffff'],
				['#980000', '#ff0000', '#ff9900', '#ffff00', '#00ff00', '#00ffff', '#4a86e8', '#0000ff', '#9900ff', '#ff00ff'],
				['#e6b8af', '#f4cccc', '#fce5cd', '#fff2cc', '#d9ead3', '#d9ead3', '#c9daf8', '#cfe2f3', '#d9d2e9', '#ead1dc'],
				['#dd7e6b', '#ea9999', '#f9cb9c', '#ffe599', '#b6d7a8', '#a2c4c9', '#a4c2f4', '#9fc5e8', '#b4a7d6', '#d5a6bd'],
				['#cc4125', '#e06666', '#f6b26b', '#ffd966', '#93c47d', '#76a5af', '#6d9eeb', '#6fa8dc', '#8e7cc3', '#c27ba0'],
				['#a61c00', '#cc0000', '#e69138', '#f1c232', '#6aa84f', '#45818e', '#3c78d8', '#3d85c6', '#674ea7', '#a64d79'],
				['#85200c', '#990000', '#b45f06', '#bf9000', '#38761d', '#134f5c', '#1155cc', '#0b5394', '#351c75', '#741b47'],
				['#5b0f00', '#660000', '#783f04', '#7f6000', '#274e13', '#0c343d', '#1c4587', '#073763', '#20124d', '#4c1130']
			];
		}

		showInput            = Boolean( ( '' === showInput ) ? false : showInput );
		showInitial          = Boolean( ( '' === showInitial ) ? false : showInitial );
		showAlpha            = Boolean( ( '' === showAlpha ) ? false : showAlpha );
		allowEmpty           = Boolean( ( '' === allowEmpty ) ? false : allowEmpty );
		showPalette          = Boolean( ( '' === showPalette ) ? false : showPalette );
		showPaletteOnly      = Boolean( ( '' === showPaletteOnly ) ? false : showPaletteOnly );
		showSelectionPalette = Boolean( ( '' === showSelectionPalette ) ? false : showSelectionPalette );
		clickoutFiresChange  = Boolean( ( '' === clickoutFiresChange ) ? false : clickoutFiresChange );
		showButtons          = Boolean( ( '' === showButtons ) ? false : showButtons );

		// Color picker options.
		colorpickerInput.spectrum(
			{
				color: currentColor,
				showAlpha: showAlpha,
				showInput: showInput,
				allowEmpty: allowEmpty,
				className: 'redux-color-rgba',
				showInitial: showInitial,
				showPalette: showPalette,
				showSelectionPalette: showSelectionPalette,
				maxPaletteSize: maxPaletteSize,
				showPaletteOnly: showPaletteOnly,
				clickoutFiresChange: clickoutFiresChange,
				chooseText: chooseText,
				cancelText: cancelText,
				showButtons: showButtons,
				containerClassName: containerClass,
				replacerClassName: replacerClass,
				preferredFormat: 'hex6',
				localStorageKey: 'redux.color-rgba.' + field_id,
				palette: palette,
				inputText: inputText,

				// On change.
				change: function( color ) {
					var blockID;
					var colorVal;
					var alphaVal;
					var rgbaVal;

					if ( null === color ) {
						if ( true === outputTransparent ) {
							colorVal = 'transparent';
						} else {
							colorVal = null;
						}
						alphaVal = null;
					} else {
						colorVal = color.toHexString();
						alphaVal = color.alpha;
					}

					if ( 'transparent' !== colorVal ) {
						rgbaVal = redux.field_objects.color_rgba.hexToRGBA( colorVal, alphaVal );
					} else {
						rgbaVal = 'transparent';
					}

					blockID = $( this ).data( 'block-id' );

					// Update HTML color value.
					el.find( 'input#' + blockID + '-color' ).val( colorVal );

					// Update HTML alpha value.
					el.find( 'input#' + blockID + '-alpha' ).val( alphaVal );

					// Update RGBA alpha value.
					el.find( 'input#' + blockID + '-rgba' ).val( rgbaVal );

					redux_change( el.find( '.redux-color-rgba-container' ) );
				}
			}
		);
	};
})( jQuery );
