<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Shortcode_Zip_Code_Checker_Widget class.
 *
 */
class Shortcode_Zip_Code_Checker_Widget {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		// Hook-in
		add_shortcode( 'zip_code_checker_widget', array( $this, 'widget_shortcode' ) );
		add_action( 'widgets_init', array( $this, 'widget_shortcode_zip_code_checker_sidebar' ), 20 );
		add_action( 'in_widget_form', array( $this, 'widget_shortcode_form' ), 10, 3 );
	}

	/**
	 * Displays a widget
	 *
	 * @param mixed args
	 * @since 1.0
	 * @return string widget output
	 */
	function do_widget( $args ) {
		global $_wp_sidebars_widgets, $wp_registered_widgets, $wp_registered_sidebars;

		extract( shortcode_atts( array(
			'id' => '',
			'title' => true, /* wheather to display the widget title */
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'before_title' => '<h2 class="widgettitle">',
			'after_title' => '</h2>',
			'after_widget' => '</div>',
			'echo' => true
		), $args, 'widget' ) );

		if( empty( $id ) || ! isset( $wp_registered_widgets[$id] ) )
			return;

		// get the widget instance options
		preg_match( '/(\d+)/', $id, $number );
		$options = get_option( $wp_registered_widgets[$id]['callback'][0]->option_name );
		$instance = $options[$number[0]];
		$class = get_class( $wp_registered_widgets[$id]['callback'][0] );
		$widgets_map = $this->widget_shortcode_get_widgets_map();
		$_original_widget_position = $widgets_map[$id];

		// maybe the widget is removed or deregistered
		if( ! $class )
			return;

		$show_title = ( '0' == $title ) ? false : true;

		/* build the widget args that needs to be filtered through dynamic_sidebar_params */
		$params = array(
			0 => array(
				'name' => $wp_registered_sidebars[$_original_widget_position]['name'],
				'id' => $wp_registered_sidebars[$_original_widget_position]['id'],
				'description' => $wp_registered_sidebars[$_original_widget_position]['description'],
				'before_widget' => $before_widget,
				'before_title' => $before_title,
				'after_title' => $after_title,
				'after_widget' => $after_widget,
				'widget_id' => $id,
				'widget_name' => $wp_registered_widgets[$id]['name']
			),
			1 => array(
				'number' => $number[0]
			)
		);
		$params = apply_filters( 'dynamic_sidebar_params', $params );

		if( ! $show_title ) {
			$params[0]['before_title'] = '<h3 class="widgettitle">';
			$params[0]['after_title'] = '</h3>';
		} elseif( is_string( $title ) && strlen( $title ) > 0 ) {
			$instance['title'] = $title;
		}

		// Substitute HTML id and class attributes into before_widget
		$classname_ = '';
		foreach ( (array) $wp_registered_widgets[$id]['classname'] as $cn ) {
			if ( is_string( $cn ) )
				$classname_ .= '_' . $cn;
			elseif ( is_object($cn) )
				$classname_ .= '_' . get_class( $cn );
		}
		$classname_ = ltrim( $classname_, '_' );
		$params[0]['before_widget'] = sprintf( $params[0]['before_widget'], $id, $classname_ );

		// render the widget
		ob_start();
		the_widget( $class, $instance, $params[0] );
		$content = ob_get_clean();

		// supress the title if we wish
		if( ! $show_title ) {
			$content = preg_replace( '/<h3 class="widgettitle">(.*?)<\/h3>/', '', $content );
		}

		if( $echo !== true )
			return $content;
		echo $content;
	}

	/**
	 * output a widget using 'widget' shortcode.
	 *
	 * Requires the widget ID.
	 * You can overwrite widget args: before_widget, before_title, after_title, after_widget
	 *
	 * @example [zip_code_checker_widget id="text-1"]
	 * @since 1.0
	 */
	function widget_shortcode( $atts, $content = null ) {
		$atts['echo'] = false;
		return $this->do_widget( $atts );
	}

	/**
	 * Registers zip_code_checker widget area
	 *
	 * Although you can use the widget shortcode for any widget in any widget area,
	 * you can use this zip_code_checker widget area for your widgets, since they don't show up
	 * in the front-end.
	 *
	 * @since 1.0
	 * @return void
	 */
	function widget_shortcode_zip_code_checker_sidebar() {
		register_sidebar( array(
			'name' => __( 'Zip Code Checker', 'sidebar_zip_code_checker' ),
			'id' => __( 'zip_code_checker', 'sidebar_zip_code_checker' ),
			'description'	=> __( 'This widget area can be used for [zip_code_checker_widget] shortcode.', 'sidebar_zip_code_checker' ),
			'before_widget' => '',
			'after_widget'	=> '',
		) );
	}

	/**
	 * Shows the shortcode for the widget
	 *
	 * @since 1.0
	 * @return void
	 */
	function widget_shortcode_form( $widget, $return, $instance ) {
		if ( $widget->name == __( 'Zip Code Checker', 'sidebar_zip_code_checker' ) ){
			echo '<p>' . __( 'Shortcode', 'sidebar_zip_code_checker' ) . ': ' . ( ( $widget->number == '__i__' ) ? __( 'Please save this first.', 'sidebar_zip_code_checker' ) : '<code>[zip_code_checker_widget id="'. $widget->id .'"]</code>' ) . '</p>';
		}
	}

	/**
	 * Returns an array of all widgets as the key, their position as the value
	 *
	 * @since 1.0
	 * @return array
	 */
	function widget_shortcode_get_widgets_map() {
		$sidebars_widgets = wp_get_sidebars_widgets();
		$widgets_map = array();
		if ( ! empty( $sidebars_widgets ) )
			foreach( $sidebars_widgets as $position => $widgets )
				if( ! empty( $widgets) )
					foreach( $widgets as $widget )
						$widgets_map[$widget] = $position;

		return $widgets_map;
	}

}

new Shortcode_Zip_Code_Checker_Widget();