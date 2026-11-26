<?php
/**
 * Extra header options for the Customizer (theme without Kubio), for the front page header
 * (AtelierWP_Header_Style_Options) and the inner pages header (AtelierWP_Inner_Header_Style_Options):
 * logo and menu colours, normal and sticky, the sticky navigation background
 * and the vertical alignment of the hero content and columns.
 *
 * The setting ids use the same convention as the existing options:
 * "<template part>.<item>.<Kubio block attribute path>", where every item has a
 * styleRef in the theme defaults. When Kubio is activated, its customizer importer
 * copies each value into the block with that styleRef; inc/kubio-header-options-import.php
 * covers the cases a styleRef alone does not reach.
 */

use ColibriWP\Theme\Components\CSSOutput;
use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\Core\Hooks;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Translations;

class AtelierWP_Header_Style_Options extends ComponentBase {

	/**
	 * The template part the options belong to.
	 */
	const PART = 'front-header';

	/**
	 * Kubio verticalAlign values and their CSS equivalents. "center" is what the header markup
	 * already uses, so it prints nothing and leaves the markup's own classes in charge.
	 */
	const VERTICAL_ALIGN = array(
		'start'  => 'flex-start',
		'center' => '',
		'end'    => 'flex-end',
	);

	/**
	 * Items added to the front page header data, with the styleRef of the block each one maps to.
	 * The nav logo is the logo in the navigation bar; the existing "logo" item points to the logo
	 * inside the mobile off-canvas panel. The items carry no values of their own, so Kubio only
	 * receives the options that were actually set.
	 */
	public static function defaultItems() {
		return array(
			'nav-logo'          => array(
				'selective_selector' => '.atelier-wp-front-header__k__jpBXcQsP_-container',
				'styleRef'           => 'jpBXcQsP_',
				'props'              => array(),
			),
			'hero-text-column'  => array(
				'selective_selector' => '.atelier-wp-front-header__k__9IGHpldIpw-container',
				'styleRef'           => '9IGHpldIpw',
				'props'              => array(),
			),
			'hero-media-column' => array(
				'selective_selector' => '.atelier-wp-front-header__k__Oe59O6QZ2-container',
				'styleRef'           => 'Oe59O6QZ2',
				'props'              => array(),
			),
			'hero-row'          => array(
				'selective_selector' => '.atelier-wp-front-header__k__bgnhUSaQMl-container',
				'styleRef'           => 'bgnhUSaQMl',
				'props'              => array(),
			),
		);
	}

	/**
	 * @param array $defaults
	 *
	 * @return array
	 */
	public static function addDefaults( $defaults ) {
		return array_replace_recursive( array( static::PART => static::defaultItems() ), $defaults );
	}

	/**
	 * The ids of the settings added here.
	 *
	 * @return string[]
	 */
	public static function settingIds() {
		return array_keys( static::getOptions()['settings'] );
	}

	/**
	 * Devices with their own values: the setting ids contain "{media}", which is empty for desktop and
	 * ".media.<device>" for tablet and mobile, the same place Kubio keeps per device values.
	 */
	const DEVICES = array( 'desktop', 'tablet', 'mobile' );

	/**
	 * Kubio verticalAlign values for tablet and mobile. These are printed for "center" too, so a
	 * device can go back to the middle when the desktop value is different.
	 */
	const VERTICAL_ALIGN_MEDIA = array(
		'start'  => 'flex-start',
		'center' => 'center',
		'end'    => 'flex-end',
	);

	/**
	 * The options of every device.
	 */
	protected static function getOptions() {
		$settings = array();

		foreach ( static::baseOptions() as $id => $setting ) {
			if ( strpos( $id, '{media}' ) === false ) {
				$settings[ $id ] = $setting;
				continue;
			}

			foreach ( static::DEVICES as $device ) {
				$settings[ static::deviceSettingId( $id, $device ) ] = static::deviceSetting( $setting, $device );
			}
		}

		// ColibriWP shows its plugin (PRO) message in a section tab only when the tab has no controls; the theme left
		// these tabs empty before the header options, so the message is added back under them. The front page and the
		// inner pages headers share the sections and this message; a high priority keeps it after the controls of both
		// (otherwise the inner header's controls, registered later, came after it)
		foreach ( array( array( 'nav_bar', 'style' ), array( 'hero', 'layout' ), array( 'front-header.logo.section', 'style' ) ) as $tab ) {
			$settings[ "{$tab[0]}-{$tab[1]}-plugin-message" ] = array(
				'control' => array(
					'type'        => 'plugin-message',
					'section'     => $tab[0],
					'colibri_tab' => $tab[1],
					'priority'    => 1000,
				),
			);
		}

		// the theme defaults (e.g. the default header preset, inc/default-header-preset.php) win over the options' own
		// fallbacks, so a fresh install renders the default preset's values (vertical alignment, colours)
		foreach ( $settings as $setting_id => $setting ) {
			if ( array_key_exists( 'default', $setting ) ) {
				$settings[ $setting_id ]['default'] = Defaults::get( $setting_id, $setting['default'] );
			}
		}
		return array( 'settings' => $settings );
	}

	/**
	 * @param string $id     setting id with the "{media}" placeholder
	 * @param string $device
	 *
	 * @return string
	 */
	public static function deviceSettingId( $id, $device ) {
		return str_replace( '{media}', $device === 'desktop' ? '' : ".media.{$device}", $id );
	}

	/**
	 * The tablet or mobile version of a setting: printed in the device's media query, with the
	 * device in the label and no default value (empty means "same as desktop").
	 *
	 * @param array  $setting
	 * @param string $device
	 *
	 * @return array
	 */
	protected static function deviceSetting( $setting, $device ) {
		if ( $device === 'desktop' ) {
			return $setting;
		}

		$media = $device === 'tablet' ? CSSOutput::tabletMedia() : CSSOutput::mobileMedia();

		$setting['default']          = '';
		$setting['control']['label'] = $setting['control']['label'] . ' (' . Translations::escHtml( $device ) . ')';

		// empty = same as desktop: the button groups' sanitize would otherwise reject it (the header presets reset the
		// tablet/mobile values with an empty one)
		if ( $setting['control']['type'] === 'button-group' ) {
			$setting['control']['none_value'] = '';
		}

		foreach ( $setting['css_output'] as $index => $output ) {
			$output['media'] = $media;

			if ( isset( $output['value'] ) && $output['value'] === static::VERTICAL_ALIGN ) {
				$output['value'] = static::VERTICAL_ALIGN_MEDIA;
			}

			$setting['css_output'][ $index ] = $output;
		}

		return $setting;
	}

	/**
	 * The ids of the settings that have a value per device, with their device.
	 *
	 * @return array
	 */
	public static function deviceSettingIds() {
		$ids = array();

		foreach ( array_keys( static::baseOptions() ) as $id ) {
			if ( strpos( $id, '{media}' ) !== false ) {
				foreach ( static::DEVICES as $device ) {
					$ids[ static::deviceSettingId( $id, $device ) ] = $device;
				}
			}
		}

		return $ids;
	}

	/**
	 * The options, with "{media}" in the ids of the options that have a value per device.
	 */
	protected static function baseOptions() {
		$p        = static::PART;
		$nav_logo = Defaults::get( "{$p}.nav-logo.selective_selector" );
		$menu     = Defaults::get( 'front-header.header-menu.selective_selector' );
		$nav      = Defaults::get( "{$p}.navigation.selective_selector" );
		$hero     = Defaults::get( "{$p}.hero.selective_selector" );

		$logo_text = "{$nav_logo} .wp-block-kubio-logo__text";
		$menu_link = "{$menu}>.kubio-menu>ul>li>a";
		$menu_icon = "{$menu} .wp-block-kubio-menu-offscreen__icon";
		$sticky    = '.h-navigation_sticky ';

		list( $menu_normal, $menu_sticky ) = static::menuScopes( $nav );

		$logo_is_text = array(
			array(
				'setting'  => 'front-header.logo.props.layoutType',
				'operator' => '=',
				'value'    => 'text',
			),
		);

		$align_choices = array(
			'start'  => Translations::escHtml( 'align_top' ),
			'center' => Translations::escHtml( 'align_middle' ),
			'end'    => Translations::escHtml( 'align_bottom' ),
		);

		$settings = array(
			// logo
			"{$p}.nav-logo.style.descendants.text{media}.typography.color"                    => array(
				'default'    => '',
				'control'    => array(
					'label'        => Translations::escHtml( 'logo_text_color' ),
					'type'         => 'color',
					'section'      => 'front-header.logo.section',
					'colibri_tab'  => 'style',
					'active_rules' => $logo_is_text,
				),
				'css_output' => array(
					array(
						'selector' => $logo_text,
						'property' => 'color',
					),
				),
			),

			"{$p}.nav-logo.style.ancestor.sticky.descendants.text{media}.typography.color"    => array(
				'default'    => '',
				'control'    => array(
					'label'        => Translations::escHtml( 'logo_sticky_text_color' ),
					'type'         => 'color',
					'section'      => 'front-header.logo.section',
					'colibri_tab'  => 'style',
					'active_rules' => $logo_is_text,
				),
				'css_output' => array(
					array(
						'selector' => $sticky . $logo_text,
						'property' => 'color',
					),
				),
			),

			// menu
			"{$p}.header-menu.style.descendants.main-menu-a{media}.typography.color"          => array(
				'default'    => '',
				'control'    => array(
					'label'       => Translations::escHtml( 'menu_items_color' ),
					'type'        => 'color',
					'section'     => 'front-header.header-menu.section',
					'colibri_tab' => 'style',
				),
				'css_output' => array(
					array(
						'selector' => $menu_normal . $menu_link,
						'property' => 'color',
					),
					// the mobile menu button uses the same colour
					array(
						'selector' => $menu_normal . $menu_icon,
						'property' => 'fill',
					),
				),
			),

			"{$p}.header-menu.style.descendants.main-menu-a.states.hover{media}.typography.color" => array(
				'default'    => '',
				'control'    => array(
					'label'       => Translations::escHtml( 'menu_hover_color' ),
					'type'        => 'color',
					'section'     => 'front-header.header-menu.section',
					'colibri_tab' => 'style',
				),
				'css_output' => static::menuHighlightOutput( $menu, $menu_normal ),
			),

			"{$p}.header-menu.separator.sticky"                                        => array(
				'default' => '',
				'control' => array(
					'label'       => '',
					'type'        => 'separator',
					'section'     => 'front-header.header-menu.section',
					'colibri_tab' => 'style',
				),
			),

			"{$p}.header-menu.style.ancestor.sticky.descendants.main-menu-a{media}.typography.color" => array(
				'default'    => '',
				'control'    => array(
					'label'       => Translations::escHtml( 'menu_sticky_items_color' ),
					'type'        => 'color',
					'section'     => 'front-header.header-menu.section',
					'colibri_tab' => 'style',
				),
				'css_output' => array(
					array(
						'selector' => $menu_sticky . $menu_link,
						'property' => 'color',
					),
					array(
						'selector' => $menu_sticky . $menu_icon,
						'property' => 'fill',
					),
				),
			),

			"{$p}.header-menu.style.ancestor.sticky.descendants.main-menu-a.states.hover{media}.typography.color" => array(
				'default'    => '',
				'control'    => array(
					'label'       => Translations::escHtml( 'menu_sticky_hover_color' ),
					'type'        => 'color',
					'section'     => 'front-header.header-menu.section',
					'colibri_tab' => 'style',
				),
				'css_output' => static::menuHighlightOutput( $menu, $menu_sticky ),
			),

			// navigation bar
			"{$p}.navigation.style.descendants.section{media}.background.color" => array(
				'default'    => '',
				'control'    => array(
					'label'       => Translations::escHtml( 'navigation_background_color' ),
					'type'        => 'color',
					'section'     => 'nav_bar',
					'colibri_tab' => 'style',
				),
				'css_output' => array(
					array(
						// the navigation outer class outranks the exported (transparent) rule; the sticky rules stay stronger
						'selector' => ".wp-block-kubio-navigation__outer {$nav}",
						'property' => 'background-color',
					),
				),
			),

			"{$p}.navigation.style.ancestor.sticky.descendants.section{media}.background.color" => array(
				'default'    => '',
				'control'    => array(
					'label'       => Translations::escHtml( 'sticky_background_color' ),
					'type'        => 'color',
					'section'     => 'nav_bar',
					'colibri_tab' => 'style',
				),
				'css_output' => array(
					array(
						// the navigation outer class outranks the exported sticky rule (.h-navigation_sticky:not(.extra-1)...)
						'selector' => ".wp-block-kubio-navigation__outer {$nav}.h-navigation_sticky",
						'property' => 'background-color',
					),
				),
			),
		);

		// hero, in the order the hero content tab shows them: where the columns sit (row), where the row sits (hero),
		// then where the content sits inside each column
		$hero_align = array(
			"{$p}.hero.props.verticalAlign"                                                => array(
				'default'    => 'center',
				'control'    => array(
					'label'        => Translations::escHtml( 'hero_content_vertical_align' ),
					'type'         => 'button-group',
					'button_size'  => 'medium',
					'choices'      => $align_choices,
					'section'      => 'hero',
					'colibri_tab'  => 'content',
					'active_rules' => array(
						array(
							'setting'  => "{$p}.hero.full_height",
							'operator' => '=',
							'value'    => true,
						),
					),
				),
				'css_output' => array(
					array(
						'selector'      => $hero,
						'property'      => 'align-items',
						'value'         => static::VERTICAL_ALIGN,
						'value_pattern' => '%s !important',
					),
				),
			),
		);

		return array_merge( $settings, static::rowOptions(), $hero_align, static::columnOptions( $align_choices ) );
	}

	/**
	 * Horizontal alignment of the hero columns in the hero row.
	 *
	 * @return array
	 */
	protected static function rowOptions() {
		$p   = static::PART;
		$row = Defaults::get( "{$p}.hero-row.selective_selector" );

		return array(
			// where the columns sit in the hero row (Kubio row "horizontalAlign"); with a column narrower than the row,
			// "start" keeps the text under the logo
			"{$p}.hero-row.props{media}.layout.horizontalAlign"        => array(
				'default'    => 'center',
				'control'    => array(
					'label'       => Translations::escHtml( 'hero_columns_horizontal_align' ),
					'type'        => 'button-group',
					'button_size' => 'medium',
					'choices'     => array(
						'start'  => Translations::escHtml( 'align_left' ),
						'center' => Translations::escHtml( 'align_center' ),
						'end'    => Translations::escHtml( 'align_right' ),
					),
					'section'     => 'hero',
					'colibri_tab' => 'content',
				),
				'css_output' => array(
					array(
						'selector'      => "{$row} .wp-block-kubio-row__inner",
						'property'      => 'justify-content',
						'value'         => static::VERTICAL_ALIGN,
						'value_pattern' => '%s !important',
					),
				),
			),
		);
	}

	/**
	 * Vertical alignment of the hero text and media columns.
	 *
	 * @param array $align_choices
	 *
	 * @return array
	 */
	protected static function columnOptions( $align_choices ) {
		$p            = static::PART;
		$text_column  = Defaults::get( "{$p}.hero-text-column.selective_selector" );
		$media_column = Defaults::get( "{$p}.hero-media-column.selective_selector" );

		return array(
			"{$p}.hero-text-column.props.layout.verticalAlign"  => array(
				'default'    => 'center',
				'control'    => array(
					'label'       => Translations::escHtml( 'hero_text_vertical_align' ),
					'type'        => 'button-group',
					'button_size' => 'medium',
					'choices'     => $align_choices,
					'section'     => 'hero',
					'colibri_tab' => 'content',
				),
				'css_output' => array(
					// the exported design centres the inner block in the column container (align-items: center, inner height auto),
					// so the column content is placed by the container; the align rule moves it inside the inner block
					array(
						'selector'      => "{$text_column}",
						'property'      => 'align-items',
						'value'         => static::VERTICAL_ALIGN,
						'value_pattern' => '%s !important',
					),
					array(
						'selector'      => "{$text_column} .wp-block-kubio-column__align",
						'property'      => 'align-self',
						'value'         => static::VERTICAL_ALIGN,
						'value_pattern' => '%s !important',
					),
				),
			),

			"{$p}.hero-media-column.props.layout.verticalAlign" => array(
				'default'    => 'center',
				'control'    => array(
					'label'        => Translations::escHtml( 'hero_media_vertical_align' ),
					'type'         => 'button-group',
					'button_size'  => 'medium',
					'choices'      => $align_choices,
					'section'      => 'hero',
					'colibri_tab'  => 'content',
					'active_rules' => array(
						array(
							'setting'  => "{$p}.hero.props.heroSection.layout",
							'operator' => '!=',
							'value'    => 'textOnly',
						),
					),
				),
				'css_output' => array(
					// the exported design centres the inner block in the column container (align-items: center, inner height auto),
					// so the column content is placed by the container; the align rule moves it inside the inner block
					array(
						'selector'      => "{$media_column}",
						'property'      => 'align-items',
						'value'         => static::VERTICAL_ALIGN,
						'value_pattern' => '%s !important',
					),
					array(
						'selector'      => "{$media_column} .wp-block-kubio-column__align",
						'property'      => 'align-self',
						'value'         => static::VERTICAL_ALIGN,
						'value_pattern' => '%s !important',
					),
				),
			),
		);
	}

	/**
	 * Selector prefixes of the menu, normal and sticky. The menu markup is shared by both headers
	 * (it is the front page header's menu), so this header's menu is the one in its navigation bar.
	 *
	 * @param string $nav navigation bar selector
	 *
	 * @return string[]
	 */
	protected static function menuScopes( $nav ) {
		return array( '', '.h-navigation_sticky ' );
	}

	/**
	 * Hover and active menu item colour, plus the highlight line drawn by the
	 * "bordered" hover effects, which follows the same colour.
	 *
	 * @param string $menu   menu selector
	 * @param string $prefix selector prefix (the sticky state)
	 *
	 * @return array
	 */
	protected static function menuHighlightOutput( $menu, $prefix ) {
		$items  = "{$prefix}{$menu}>.kubio-menu>ul>li";
		$output = array();

		foreach ( array( '>a:hover', '.current-menu-item>a', '.current_page_item>a' ) as $state ) {
			$output[] = array(
				'selector' => $items . $state,
				'property' => 'color',
			);
		}

		foreach ( array( ':before', ':after' ) as $line ) {
			$output[] = array(
				'selector' => "{$prefix}{$menu}.bordered-active-item>.kubio-menu>ul>li{$line}",
				'property' => 'background-color',
			);
		}

		return $output;
	}

	/**
	 * Active rules of these options look at settings of other components (logo type, hero layout,
	 * full height); fall back to the theme defaults for those.
	 */
	public static function settingDefault( $name, $fallback = null ) {
		$default = parent::settingDefault( $name, null );

		return $default !== null ? $default : Defaults::get( $name, $fallback );
	}

	public function renderContent( $parameters = array() ) {
		// options only: the CSS is printed by the theme's CSS output component
	}
}

/**
 * The same options for the header of the inner pages. Its hero has a single (text) column,
 * so it has no column alignment options.
 */
class AtelierWP_Inner_Header_Style_Options extends AtelierWP_Header_Style_Options {

	const PART = 'header';

	public static function defaultItems() {
		return array(
			'nav-logo' => array(
				'selective_selector' => '.atelier-wp-header__k__J6McH4z0dR0-container',
				'styleRef'           => 'J6McH4z0dR0',
				'props'              => array(),
			),
		);
	}

	protected static function rowOptions() {
		return array();
	}

	protected static function columnOptions( $align_choices ) {
		return array();
	}

	/**
	 * The menu inside the inner pages navigation bar. The normal state selector is wrapped in
	 * :where() so it has the specificity of the front page header's menu colours: it overrides them,
	 * but the front page header's sticky colours still apply when this header has none of its own,
	 * which is also how the colours are carried into Kubio.
	 */
	protected static function menuScopes( $nav ) {
		return array( ":where({$nav}) ", "{$nav}.h-navigation_sticky " );
	}
}

Hooks::prefixed_add_filter( 'defaults', array( 'AtelierWP_Header_Style_Options', 'addDefaults' ) );
Hooks::prefixed_add_filter( 'defaults', array( 'AtelierWP_Inner_Header_Style_Options', 'addDefaults' ) );

/**
 * Customizer controls are shown only for components rendered in the preview, so "render" the
 * options of the header shown on the current page (they print nothing).
 */
Hooks::prefixed_add_action(
	'before_header',
	function () {
		$component = \ColibriWP\Theme\View::isFrontPage() ? 'header-style-options' : 'inner-header-style-options';
		\ColibriWP\Theme\Theme::getInstance()->get( $component )->render();
	}
);

Hooks::prefixed_add_filter(
	'components',
	function ( $components ) {
		$components['header-style-options']       = AtelierWP_Header_Style_Options::class;
		$components['inner-header-style-options'] = AtelierWP_Inner_Header_Style_Options::class;

		return $components;
	},
	30
);

/**
 * The options with a value per device show only the control of the device previewed in the
 * Customizer (the device buttons at the bottom of the panel), so a colour picked while previewing
 * on mobile is the mobile colour.
 */
add_action(
	'customize_controls_enqueue_scripts',
	function () {
		$devices = array_merge(
			AtelierWP_Header_Style_Options::deviceSettingIds(),
			AtelierWP_Inner_Header_Style_Options::deviceSettingIds()
		);

		$script = <<<'JS'
(function ( api, devices ) {
	var key = function ( id ) {
		return String( id ).replace( /\./g, '-' ).toLowerCase();
	};
	var byKey = {};

	Object.keys( devices ).forEach( function ( id ) {
		byKey[ key( id ) ] = devices[ id ];
	} );

	var toggle = function ( control ) {
		var device = byKey[ key( control.id ) ];

		if ( ! device && control.setting ) {
			device = byKey[ key( control.setting.id ) ];
		}

		if ( device ) {
			control.container.toggleClass( 'atelier-wp-other-device', device !== api.previewedDevice.get() );
		}
	};

	api.bind( 'ready', function () {
		api.control.each( toggle );
		api.control.bind( 'add', toggle );
		api.previewedDevice.bind( function () {
			api.control.each( toggle );
		} );
	} );
})( wp.customize, %s );
JS;

		wp_add_inline_script( 'customize-controls', sprintf( $script, wp_json_encode( $devices ) ) );
		wp_add_inline_style( 'customize-controls', '.atelier-wp-other-device{display:none !important}' );
	}
);

/**
 * Clearing a colour picker sends null, and wp.customize ignores a null value, so the previous colour stays and
 * gets published. For these options empty means "the theme's own colour": null becomes "".
 */
add_action(
	'customize_controls_enqueue_scripts',
	function () {
		$ids    = array_merge( AtelierWP_Header_Style_Options::settingIds(), AtelierWP_Inner_Header_Style_Options::settingIds() );
		$script = '(function (ids) {
			ids.forEach(function (id) {
				wp.customize(id, function (setting) {
					var validate = setting.validate;
					setting.validate = function (value) {
						return validate.call(this, value === null ? "" : value);
					};
				});
			});
		})(%s);';

		wp_add_inline_script( 'customize-controls', sprintf( $script, wp_json_encode( array_values( $ids ) ) ) );
	}
);
