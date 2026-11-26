<?php
/**
 * Carries the header options from inc/header-style-options.php into Kubio when Kubio is activated.
 *
 * Kubio's customizer importer (Kubio\Core\CustomizerImporter) reads the theme mods, groups them
 * by item ("front-header.<item>", "header.<item>") and merges each item's "style" and "props" into the block whose
 * styleRef matches the item's styleRef. That already handles the menu, the navigation bar, the hero
 * and the hero columns. This file covers what a styleRef alone does not:
 *
 * - empty values (a colour that was cleared) must not overwrite the block's own colours;
 * - the logo in the navigation bar can have a different styleRef in Kubio's template than in
 *   the theme, so the logo colours are set on every logo of the navigation bar (not the one in
 *   the mobile off-canvas panel);
 * - one theme option sets several Kubio attributes: the hover colour is also the "active" colour
 *   and the colour of the hover line, the menu colour is also the mobile menu button colour,
 *   and Kubio's tablet/mobile overrides of the same colours are replaced as well;
 * - the tablet and mobile values of the options go to Kubio's per device values.
 */

use ColibriWP\Theme\Core\ComponentBase;

/**
 * The ids of the header options of both headers.
 *
 * @return string[]
 */
function atelier_wp_header_options_ids() {
	return array_merge( AtelierWP_Header_Style_Options::settingIds(), AtelierWP_Inner_Header_Style_Options::settingIds() );
}

/**
 * Header options with a value, by setting id.
 *
 * @return array
 */
function atelier_wp_header_options_values() {
	$values = array();

	foreach ( atelier_wp_header_options_ids() as $id ) {
		$value = ComponentBase::mabyDeserializeModValue( get_theme_mod( $id, '' ) );

		if ( is_string( $value ) && trim( $value ) !== '' ) {
			$values[ $id ] = trim( $value );
		}
	}

	return $values;
}

/**
 * While Kubio imports its templates, hide the header options that are empty, so Kubio's importer
 * does not copy an empty colour over the block's own colour.
 *
 * @param array $mods
 *
 * @return array
 */
function atelier_wp_kubio_hide_empty_header_options( $mods ) {
	if ( ! is_array( $mods ) ) {
		return $mods;
	}

	foreach ( atelier_wp_header_options_ids() as $id ) {
		if ( array_key_exists( $id, $mods ) && ( $mods[ $id ] === null || ( is_string( $mods[ $id ] ) && trim( $mods[ $id ] ) === '' ) ) ) {
			unset( $mods[ $id ] );
		}
	}

	return $mods;
}

// Kubio has no "before activation" action: the first content its importer filters is the start of the import, before
// its customizer importer (priority 20) reads the theme mods. Hiding the empty options only then keeps
// CustomizerImporter::themeHasModifiedOptions(), checked earlier, seeing every theme mod.
add_filter(
	'kubio/importer/content',
	function ( $content ) {
		add_filter( 'option_theme_mods_' . get_option( 'stylesheet' ), 'atelier_wp_kubio_hide_empty_header_options' );

		return $content;
	},
	1
);

add_action(
	'kubio/after_activation',
	function () {
		remove_filter( 'option_theme_mods_' . get_option( 'stylesheet' ), 'atelier_wp_kubio_hide_empty_header_options' );
	}
);

/**
 * Small helpers for dotted paths in nested arrays.
 */
function atelier_wp_array_get( $array, $path, $default = null ) {
	foreach ( explode( '.', $path ) as $key ) {
		if ( ! is_array( $array ) || ! array_key_exists( $key, $array ) ) {
			return $default;
		}
		$array = $array[ $key ];
	}

	return $array;
}

function atelier_wp_array_set( &$array, $path, $value ) {
	$keys = explode( '.', $path );
	$ref  = &$array;

	foreach ( $keys as $key ) {
		if ( ! isset( $ref[ $key ] ) || ! is_array( $ref[ $key ] ) ) {
			$ref[ $key ] = array();
		}
		$ref = &$ref[ $key ];
	}

	$ref = $value;
}

/**
 * Removes a value, and the arrays left empty by its removal.
 */
function atelier_wp_array_unset( &$array, $path ) {
	$keys = explode( '.', $path );
	$key  = array_shift( $keys );

	if ( ! is_array( $array ) || ! array_key_exists( $key, $array ) ) {
		return;
	}

	if ( ! $keys ) {
		unset( $array[ $key ] );

		return;
	}

	atelier_wp_array_unset( $array[ $key ], implode( '.', $keys ) );

	if ( $array[ $key ] === array() ) {
		unset( $array[ $key ] );
	}
}

/**
 * The values of an option per device, desktop first.
 *
 * @param array  $values header options with a value
 * @param string $id     setting id with the "{media}" placeholder
 *
 * @return array device => value
 */
function atelier_wp_header_option_by_device( $values, $id ) {
	$by_device = array();

	foreach ( AtelierWP_Header_Style_Options::DEVICES as $device ) {
		$device_id = AtelierWP_Header_Style_Options::deviceSettingId( $id, $device );

		if ( isset( $values[ $device_id ] ) ) {
			$by_device[ $device ] = $values[ $device_id ];
		}
	}

	return $by_device;
}

/**
 * Sets the values of an option per device in a block attribute. Only the devices with a value of
 * their own get one, except for the sticky state (see below).
 *
 * A desktop value removes the tablet/mobile values the block already has, so those devices inherit
 * it, as they do in the theme, and keep following the desktop value when it is changed in Kubio.
 * The sticky state is the exception: on tablet and mobile Kubio does not inherit the desktop sticky
 * values, it falls back to the normal state ones, so the desktop sticky value is also written for
 * the devices without a value of their own (Kubio's own templates do the same).
 *
 * @param array  $data      the block's kubio attribute, or part of it
 * @param string $path      the attribute path, with "{media}" where Kubio keeps per device values
 * @param array  $by_device device => value
 */
function atelier_wp_kubio_set_by_device( &$data, $path, $by_device ) {
	foreach ( $by_device as $device => $value ) {
		atelier_wp_array_set( $data, AtelierWP_Header_Style_Options::deviceSettingId( $path, $device ), $value );

		if ( $device === 'desktop' ) {
			foreach ( array( 'tablet', 'mobile' ) as $media ) {
				$media_path = AtelierWP_Header_Style_Options::deviceSettingId( $path, $media );

				if ( isset( $by_device[ $media ] ) ) {
					continue;
				}

				if ( strpos( $path, '.ancestor.sticky.' ) !== false ) {
					atelier_wp_array_set( $data, $media_path, $value );
				} else {
					atelier_wp_array_unset( $data, $media_path );
				}
			}
		}
	}
}

/**
 * Applies the header options to the blocks of a header template part.
 *
 * @param array    $blocks    parsed blocks
 * @param array    $values    header options with a value
 * @param string   $part      the options' template part: "front-header" or "header"
 * @param string[] $ancestors names of the ancestor blocks
 *
 * @return array
 */
function atelier_wp_kubio_apply_header_options( $blocks, $values, $part, $ancestors = array() ) {
	$get = function ( $id ) use ( $values, $part ) {
		return atelier_wp_header_option_by_device( $values, "{$part}.{$id}" );
	};

	foreach ( $blocks as $index => $block ) {
		$name = isset( $block['blockName'] ) ? $block['blockName'] : null;

		if ( $name && isset( $block['attrs']['kubio'] ) && is_array( $block['attrs']['kubio'] ) ) {
			$kubio        = $block['attrs']['kubio'];
			$in_nav       = in_array( 'kubio/navigation', $ancestors, true );
			$in_offscreen = in_array( 'kubio/menu-offscreen', $ancestors, true );

			// logo of the navigation bar
			if ( $name === 'kubio/logo' && $in_nav && ! $in_offscreen ) {
				foreach ( array( '', 'ancestor.sticky.' ) as $state ) {
					$path = "style.{$state}descendants.text{media}.typography.color";
					atelier_wp_kubio_set_by_device( $kubio, $path, $get( 'nav-logo.' . $path ) );
				}
			}

			// menu of the navigation bar
			if ( $name === 'kubio/dropdown-menu' && $in_nav ) {
				foreach ( array( '', 'ancestor.sticky.' ) as $state ) {
					$path = "style.{$state}descendants.main-menu-a{media}.typography.color";
					atelier_wp_kubio_set_by_device( $kubio, $path, $get( 'header-menu.' . $path ) );

					// the hover colour is also the active colour and the colour of the hover line
					$hover = $get( "header-menu.style.{$state}descendants.main-menu-a.states.hover{media}.typography.color" );
					atelier_wp_kubio_set_by_device( $kubio, "style.{$state}descendants.main-menu-a.states.hover{media}.typography.color", $hover );
					atelier_wp_kubio_set_by_device( $kubio, "style.{$state}descendants.main-menu-a.states.menuButtonActive{media}.typography.color", $hover );
					atelier_wp_kubio_set_by_device( $kubio, "style.{$state}descendants.hover-effect-border{media}.background.color", $hover );
				}
			}

			// mobile menu button: the menu items colour
			if ( $name === 'kubio/menu-offscreen' && $in_nav ) {
				foreach ( array( '', 'ancestor.sticky.' ) as $state ) {
					$color = $get( "header-menu.style.{$state}descendants.main-menu-a{media}.typography.color" );
					atelier_wp_kubio_set_by_device( $kubio, "style.{$state}descendants.icon{media}.fill", $color );
				}
			}

			// navigation background, normal and sticky
			if ( $name === 'kubio/navigation' ) {
				foreach ( array( '', 'ancestor.sticky.' ) as $state ) {
					$path = "style.{$state}descendants.section{media}.background.color";
					atelier_wp_kubio_set_by_device( $kubio, $path, $get( 'navigation.' . $path ) );
				}
			}

			// hero content and columns
			if ( $name === 'kubio/hero' ) {
				atelier_wp_kubio_set_by_device( $kubio, 'props{media}.verticalAlign', $get( 'hero.props{media}.verticalAlign' ) );
			}

			if ( $name === 'kubio/row' && in_array( 'kubio/hero', $ancestors, true ) ) {
				atelier_wp_kubio_set_by_device( $kubio, 'props{media}.layout.horizontalAlign', $get( 'hero-row.props{media}.layout.horizontalAlign' ) );
			}

			if ( $name === 'kubio/column' && in_array( 'kubio/hero', $ancestors, true ) ) {
				$column_type = atelier_wp_array_get( $kubio, 'props.internal.heroSection.type' );
				$item        = null;

				if ( $column_type === 'text' ) {
					$item = 'hero-text-column';
				} elseif ( $column_type && $column_type !== 'none' ) {
					$item = 'hero-media-column';
				}

				if ( $item ) {
					atelier_wp_kubio_set_by_device( $kubio, 'props{media}.layout.verticalAlign', $get( "{$item}.props{media}.layout.verticalAlign" ) );
				}
			}

			$block['attrs']['kubio'] = $kubio;
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$block['innerBlocks'] = atelier_wp_kubio_apply_header_options( $block['innerBlocks'], $values, $part, array_merge( $ancestors, array( $name ) ) );
		}

		$blocks[ $index ] = $block;
	}

	return $blocks;
}

/**
 * Runs after Kubio's own customizer importer (priority 20). The front page header gets the
 * "front-header.*" options; the inner pages header and the 404 page header get the "header.*" ones.
 */
add_filter(
	'kubio/importer/content',
	function ( $content, $type = '', $slug = '' ) {
		$parts = array(
			'front-header' => 'front-header',
			'header'       => 'header',
			'404-header'   => 'header',
		);

		if ( $type !== 'wp_template_part' || ! isset( $parts[ $slug ] ) || ! is_string( $content ) || $content === '' ) {
			return $content;
		}

		$part   = $parts[ $slug ];
		$values = array();

		$all    = atelier_wp_header_options_values();

		foreach ( $all as $id => $value ) {
			if ( strpos( $id, "{$part}." ) === 0 ) {
				$values[ $id ] = $value;
			}

			// without Kubio the inner pages show the front page header's menu, with its colours,
			// unless the inner pages header has its own
			if ( $part === 'header' && strpos( $id, 'front-header.header-menu.' ) === 0 ) {
				$inner_id = 'header.' . substr( $id, strlen( 'front-header.' ) );

				if ( ! isset( $all[ $inner_id ] ) ) {
					$values[ $inner_id ] = $value;
				}
			}
		}

		if ( ! $values ) {
			return $content;
		}

		$blocks = atelier_wp_kubio_apply_header_options( parse_blocks( $content ), $values, $part );

		return function_exists( 'kubio_serialize_blocks' ) ? kubio_serialize_blocks( $blocks ) : serialize_blocks( $blocks );
	},
	30,
	3
);
