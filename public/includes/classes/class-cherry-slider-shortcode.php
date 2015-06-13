<?php
/**
 * Cherry Slider.
 *
 * @package   Cherry_Slider
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

/**
 * Class for Slider shortcode.
 *
 * @since 1.0.0
 */
class Cherry_Slider_Shortcode extends Cherry_Slider_Data {

	/**
	 * Shortcode name.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public static $name = 'slider';

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Register shortcode on 'init'.
		add_action( 'init', array( $this, 'register_shortcode' ) );

		// Register shortcode and add it to the dialog.
		add_filter( 'cherry_shortcodes/data/shortcodes', array( $this, 'shortcodes' ) );
		add_filter( 'cherry_templater/data/shortcodes',  array( $this, 'shortcodes' ) );

		add_filter( 'cherry_editor_target_dirs', array( $this, 'add_target_dir' ), 11 );
	}

	/**
	 * Registers the [$this->name] shortcode.
	 *
	 * @since 1.0.0
	 */
	public function register_shortcode() {
		/**
		 * Filters a shortcode name.
		 *
		 * @since 1.0.0
		 * @param string $this->name Shortcode name.
		 */
		$tag = apply_filters( self::$name . '_shortcode_name', self::$name );

		add_shortcode( 'cherry_' . $tag, array( $this, 'do_shortcode' ) );
	}

	/**
	 * Filter to modify original shortcodes data and add [$this->name] shortcode.
	 *
	 * @since  1.0.0
	 * @param  array   $shortcodes Original plugin shortcodes.
	 * @return array               Modified array.
	 */
	public function shortcodes( $shortcodes ) {
		$shortcodes[ self::$name ] = array(
			'name'  => __( 'Slider', 'cherry-slider' ), // Shortcode name.
			'desc'  => 'This is a Slider Shortcode',
			'type'  => 'single', // Can be 'wrap' or 'single'. Example: [b]this is wrapped[/b], [this_is_single]
			'group' => 'content', // Can be 'content', 'box', 'media' or 'other'. Groups can be mixed, for example 'content box'.
			'atts'  => array( // List of shortcode params (attributes).
					'custom_class' => array(
						'default' => '',
						'name'    => __( 'Class', 'cherry-slider' ),
						'desc'    => __( 'Extra CSS class', 'cherry-slider' )
					),
				),
			'icon'     => 'fa fa-eye', // Custom icon (font-awesome).
			'function' => array( $this, 'do_shortcode' ) // Name of shortcode function.
		);

		return $shortcodes;
	}

	public function add_target_dir( $target_dirs ) {
		array_push( $target_dirs, CHERRY_SLIDER_DIR );

		return $target_dirs;
	}

	/**
	 * The shortcode function.
	 *
	 * @since  1.0.0
	 * @param  array  $atts      The user-inputted arguments.
	 * @param  string $content   The enclosed content (if the shortcode is used in its enclosing form).
	 * @param  string $shortcode The shortcode tag, useful for shared callback functions.
	 * @return string
	 */
	public function do_shortcode( $atts, $content = null, $shortcode = '' ) {

		// Set up the default arguments.
		$defaults = array(
			'custom_class'   => '',
		);

		/**
		 * Parse the arguments.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/shortcode_atts
		 */
		$atts = shortcode_atts( $defaults, $atts, $shortcode );


		return $this->the_slider( $atts );
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

Cherry_Slider_Shortcode::get_instance();