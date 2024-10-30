<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://test.com
 * @since      1.0.0
 *
 * @package    Ss_Jvzoo_Wpestore
 * @subpackage Ss_Jvzoo_Wpestore/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Ss_Jvzoo_Wpestore
 * @subpackage Ss_Jvzoo_Wpestore/includes
 * @author     Simon shrestha <cimon77@gmail.com>
 */
class Ss_Jvzoo_Wpestore_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ss-jvzoo-wpestore',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}
}
