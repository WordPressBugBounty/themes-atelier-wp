<?php
/**
 * The theme's own front page header is one of its header presets.
 *
 * The defaults of the front page header are replaced by the data of preset atelier_wp_default_header_preset()
 * (inc/customizer-headers.php and its JSON copy resources/header-presets/presets.json, both generated from
 * assets/headers/header-N/header.json by doHeaders in webpack.mix.js): the same values the preset
 * sets when it is picked in the Customizer, so a fresh install looks like that preset, picking it again changes
 * nothing, and Kubio converts an untouched site from the matching with-front design.
 */

use ColibriWP\Theme\Core\Hooks;
use ColibriWP\Theme\Core\Utils;
use ColibriWP\Theme\Theme;

/**
 * Number of the header preset the theme starts from (1-based, as in Customizer > Header).
 *
 * @return int
 */
function atelier_wp_default_header_preset() {
	return (int) Hooks::prefixed_apply_filters( 'default_header_preset', 1 );
}

/**
 * A preset value as HeaderPresets hands it to the Customizer: JSON (or url-encoded JSON) decoded, "%s" replaced
 * by the header presets folder URL.
 *
 * @param mixed  $value
 * @param string $base
 *
 * @return mixed
 */
function atelier_wp_header_preset_value( $value, $base ) {
	if ( is_string( $value ) && strlen( trim( $value ) ) ) {
		$decoded = json_decode( urldecode( $value ), true );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			$decoded = json_decode( $value, true );
		}
		if ( json_last_error() === JSON_ERROR_NONE && is_array( $decoded ) ) {
			$value = $decoded;
		}
	}
	if ( is_array( $value ) ) {
		foreach ( $value as $key => $item ) {
			$value[ $key ] = atelier_wp_header_preset_value( $item, $base );
		}
		return $value;
	}
	return is_string( $value ) ? sprintf( $value, $base ) : $value;
}

Hooks::prefixed_add_filter(
	'defaults',
	function ( $defaults ) {
		// the JSON copy of inc/customizer-headers.php (written next to it by the build): including the PHP file here,
		// before the Customizer's HeaderPresets does with require_once, would leave HeaderPresets without presets
		$file = Theme::rootDirectory() . '/resources/header-presets/presets.json';
		if ( ! file_exists( $file ) ) {
			return $defaults;
		}
		$headers = json_decode( file_get_contents( $file ), true );
		$data    = Utils::pathGet( (array) $headers, ( atelier_wp_default_header_preset() - 1 ) . '.data' );
		if ( ! is_array( $data ) ) {
			return $defaults;
		}
		$base = Theme::rootDirectoryUri() . '/resources/header-presets';
		foreach ( $data as $path => $value ) {
			// an empty value is "not set" for an option without a default (the header colour options): as a default
			// it would reach Kubio's customizer importer and clear the colours of the converted header
			if ( is_string( $value ) && trim( $value ) === '' && Utils::pathGet( $defaults, $path ) === null ) {
				continue;
			}
			Utils::pathSet( $defaults, $path, atelier_wp_header_preset_value( $value, $base ) );

			// the hero spacing options are saved as "<hero>.style.padding.*" but read their default from
			// "<hero>.style.descendants.outer.padding.*", so the preset's spacing is copied there too
			if ( preg_match( '/^((?:front-)?header\.hero\.style)\.(padding\..+)$/', $path, $match ) ) {
				Utils::pathSet( $defaults, "{$match[1]}.descendants.outer.{$match[2]}", $value );
			}
		}
		return $defaults;
	},
	5
);
