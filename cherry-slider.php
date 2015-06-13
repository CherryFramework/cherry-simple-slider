<?php
/**
 * Plugin Name: Cherry Slider
 * Plugin URI:  http://www.cherryframework.com/
 * Description: A Slider plugin for WordPress.
 * Version:     1.0.0 beta
 * Author:      Cherry Team
 * Author URI:  http://www.cherryframework.com/
 * Text Domain: cherry-slider
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

// If class 'Cherry_Slider' not exists.
if ( !class_exists( 'Cherry_Slider' ) ) {

	/**
	 * Sets up and initializes the Cherry_Slider plugin.
	 *
	 * @since 1.0.0
	 */
	class Cherry_Slider {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Set the constants needed by the plugin.
			add_action( 'plugins_loaded', array( $this, 'constants' ), 1 );

			// Internationalize the text strings used.
			add_action( 'plugins_loaded', array( $this, 'lang' ),      2 );

			// Load the functions files.
			add_action( 'plugins_loaded', array( $this, 'includes' ),  3 );

			// Load the admin files.
			add_action( 'plugins_loaded', array( $this, 'admin' ),     4 );

			// Load public-facing style sheet.
			add_action( 'wp_enqueue_scripts',         array( $this, 'enqueue_styles' ) );
			add_filter( 'cherry_compiler_static_css', array( $this, 'add_style_to_compiler' ) );
			// Load public-facing JavaScript.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Register activation and deactivation hook.
			register_activation_hook( __FILE__, array( $this, 'activation'     ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
		}

		/**
		 * Defines constants for the plugin.
		 *
		 * @since 1.0.0
		 */
		function constants() {

			/**
			 * Set constant name for the post type name.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_SLIDER_NAME', 'cherry_slider' );

			/**
			 * Set the version number of the plugin.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_SLIDER_VERSION', '1.0.0' );

			/**
			 * Set the slug of the plugin.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_SLIDER_SLUG', basename( dirname( __FILE__ ) ) );

			/**
			 * Set the name for the 'meta_key' value in the 'wp_postmeta' table.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_SLIDER_POSTMETA', '_cherry_slider' );

			/**
			 * Set constant path to the plugin directory.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_SLIDER_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

			/**
			 * Set constant path to the plugin URI.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_SLIDER_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
		}

		/**
		 * Loads files from the '/inc' folder.
		 *
		 * @since 1.0.0
		 */
		function includes() {
			require_once( trailingslashit( CHERRY_SLIDER_DIR ) . 'public/includes/classes/aq_resizer.php' );
			require_once( trailingslashit( CHERRY_SLIDER_DIR ) . 'public/includes/classes/class-cherry-slider-registration.php' );
			require_once( trailingslashit( CHERRY_SLIDER_DIR ) . 'public/includes/classes/class-cherry-slider-options.php' );
			require_once( trailingslashit( CHERRY_SLIDER_DIR ) . 'public/includes/classes/class-cherry-slider-data.php' );
			require_once( trailingslashit( CHERRY_SLIDER_DIR ) . 'public/includes/classes/class-cherry-slider-shortcode.php' );
		}

		/**
		 * Loads the translation files.
		 *
		 * @since 1.0.0
		 */
		function lang() {
			load_plugin_textdomain( 'cherry-slider', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Loads admin files.
		 *
		 * @since 1.0.0
		 */
		function admin() {
			if ( is_admin() ) {
				require_once( CHERRY_SLIDER_DIR . 'admin/includes/class-cherry-slider-admin.php' );
				require_once( CHERRY_SLIDER_DIR . 'admin/includes/class-cherry-update/class-cherry-plugin-update.php' );

				$Cherry_Plugin_Update = new Cherry_Plugin_Update();
				$Cherry_Plugin_Update -> init( array(
						'version'			=> CHERRY_SLIDER_VERSION,
						'slug'				=> CHERRY_SLIDER_SLUG,
						'repository_name'	=> CHERRY_SLIDER_SLUG
				));
			}
		}

		/**
		 * Register and enqueue public-facing style sheet.
		 *
		 * @since 1.0.0
		 */
		public function enqueue_styles() {
			wp_enqueue_style( 'slider-pro-style', plugins_url( 'public/assets/css/slider-pro.css', __FILE__ ), array(), CHERRY_SLIDER_VERSION );
			wp_enqueue_style( 'cherry-slider-style', plugins_url( 'public/assets/css/style.css', __FILE__ ), array(), CHERRY_SLIDER_VERSION );
		}

		/**
		 * Pass style handle to CSS compiler.
		 *
		 * @since 1.0.0
		 *
		 * @param array $handles CSS handles to compile.
		 */
		function add_style_to_compiler( $handles ) {
			$handles = array_merge(
				array(
					'slider-pro-style'    => plugins_url( 'public/assets/css/slider-pro.css', __FILE__ ),
					'cherry-slider-style' => plugins_url( 'public/assets/css/style.css', __FILE__ ),
				),
				$handles
			);

			return $handles;
		}

		/**
		 * Register and enqueue public-facing style sheet.
		 *
		 * @since 1.0.0
		 */
		public function enqueue_scripts() {
			wp_enqueue_script( 'slider-pro-script', trailingslashit( CHERRY_SLIDER_URI ) . 'public/assets/js/jquery-slider-pro.js', array( 'jquery' ), CHERRY_SLIDER_VERSION);
			wp_enqueue_script( 'cherry-slider-script', trailingslashit( CHERRY_SLIDER_URI ) . 'public/assets/js/cherry-slider-scripts.js', array( 'jquery' ), CHERRY_SLIDER_VERSION);
		}

		/**
		 * On plugin activation.
		 *
		 * @since 1.0.0
		 */
		function activation() {
			flush_rewrite_rules();
		}

		/**
		 * On plugin deactivation.
		 *
		 * @since 1.0.0
		 */
		function deactivation() {
			flush_rewrite_rules();
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance )
				self::$instance = new self;

			return self::$instance;
		}
	}

	Cherry_Slider::get_instance();
}