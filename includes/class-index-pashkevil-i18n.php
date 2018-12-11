<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       avitrop
 * @since      1.0.0
 *
 * @package    Index_Pashkevil
 * @subpackage Index_Pashkevil/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Index_Pashkevil
 * @subpackage Index_Pashkevil/includes
 * @author     tziki trop <avitrop@gmail.com>
 */
class Index_Pashkevil_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'index-pashkevil',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
