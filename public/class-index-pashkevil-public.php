<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       avitrop
 * @since      1.0.0
 *
 * @package    Index_Pashkevil
 * @subpackage Index_Pashkevil/public
 */

/**
 * The public-facing functionality of the plugin.
 *fdfd
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Index_Pashkevil
 * @subpackage Index_Pashkevil/public
 * @author     tziki trop <avitrop@gmail.com>
 */
class Index_Pashkevil_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Index_Pashkevil_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Index_Pashkevil_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/index-pashkevil-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Index_Pashkevil_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Index_Pashkevil_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( 'loadmore_ajax', plugin_dir_url( __FILE__ ) .  'js//load-more-js.js', array('jquery') , $this->version, true );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/index-pashkevil-public.js', array( 'jquery' ), $this->version, false );

	}

}
