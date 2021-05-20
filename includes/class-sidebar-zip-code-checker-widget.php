<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Sidebar_Zip_Code_Checker_Widget class.
 *
 * @extends WP_Widget
 */
class Sidebar_Zip_Code_Checker_Widget extends WP_Widget {

	private $instance = '';
	private $options = array();

    /**
     * Sidebar_Zip_Code_Checker_Widget function.
     *
     * @access public
     * @return void
     */
    public function Sidebar_Zip_Code_Checker_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'description' => __( 'Displays a zip code checker area in the sidebar.', 'sidebar_zip_code_checker' ) );

		/* Create the widget. */
		$this->WP_Widget( 'wp_sidebarzipcodechecker', __( 'Zip Code Checker', 'sidebar_zip_code_checker' ), $widget_ops );
    }

    /**
     * define_options function.
     *
     * @access public
     * @return void
     */
    public function define_options() {
	    // Define options for widget
		$this->options       = array(
			'zip_code_checker_title' => array(
				'label'           => __( 'Title', 'sidebar_zip_code_checker' ),
				'default'         => __( 'Check Product Availability At', 'sidebar_zip_code_checker' ),
				'type'            => 'text'
			),
			'zip_code_checker_cod'  => array(
				'label'           => __( 'Display COD status', 'sidebar_zip_code_checker' ),
				'default'         => 1,
				'type'            => 'checkbox'
			),
			'zip_code_checker_display_company_name'  => array(
				'label'           => __( 'Display Company Name', 'sidebar_zip_code_checker' ),
				'default'         => 1,
				'type'            => 'checkbox'
			),
			'break-1'           => array(
				'type'            => 'break'
			),
			'zip_code_checker_change_font_size'  => array(
				'label'           => __( 'Change Font Size', 'sidebar_zip_code_checker' ),
				'default'         => __( '', 'sidebar_zip_code_checker' ),
				'type'            => 'text'
			),
			'zip_code_checker_change_color'  => array(
				'label'           => __( 'Change Color', 'sidebar_zip_code_checker' ),
				'default'         => __( '', 'sidebar_zip_code_checker' ),
				'type'            => 'text'
			),
			'zip_code_checker_change_font_family'  => array(
				'label'           => __( 'Change Font Family', 'sidebar_zip_code_checker' ),
				'default'         => __( '', 'sidebar_zip_code_checker' ),
				'type'            => 'text'
			)
		);
    }

    /**
     * widget function.
     *
     * @access public
     * @param mixed $args
     * @param mixed $instance
     * @return void
     */
    public function widget( $args, $instance ) {

		// Filter can be used to conditonally hide the widget
		if ( ! apply_filters( 'sidebar_zip_code_checker_widget_display', true ) )
			return;

		// Record $instance
		$this->instance = $instance;

		$defaults = array(
			'zip_code_checker_title' 				=> ! empty( $instance['zip_code_checker_title'] ) ? $instance['zip_code_checker_title'] : __( 'Check Product Availability At', 'sidebar_zip_code_checker' ),
			'zip_code_checker_cod'      			=> isset( $instance['zip_code_checker_cod'] ) ? $instance['zip_code_checker_cod'] : 1,
			'zip_code_checker_display_company_name' => isset( $instance['zip_code_checker_display_company_name'] ) ? $instance['zip_code_checker_display_company_name'] : 1,
			'zip_code_checker_change_font_size'  	=> ! empty( $instance['zip_code_checker_change_font_size'] ) ? $instance['zip_code_checker_change_font_size'] : __( '', 'sidebar_zip_code_checker' ),
			'zip_code_checker_change_color'  		=> ! empty( $instance['zip_code_checker_change_color'] ) ? $instance['zip_code_checker_change_color'] : __( '', 'sidebar_zip_code_checker' ),
			'zip_code_checker_change_family'  		=> ! empty( $instance['zip_code_checker_change_family'] ) ? $instance['zip_code_checker_change_family'] : __( 'inherit', 'sidebar_zip_code_checker' )
		);

		$args = array_merge( $defaults, $args );

		extract( $args );

		echo $before_widget;

		do_action( 'sidebar_zip_code_checker_widget_start' );

		$zip_code_checker_title = apply_filters( 'sidebar_zip_code_checker_widget_zip_code_checker_title', $zip_code_checker_title );

		if ( $zip_code_checker_title )
			echo $before_title . $zip_code_checker_title . $after_title;

		$defaults = array(
			'form_id' => 'zipcodecheckerform',
			'label_verify' => __( 'Verify' ),
			'id_zip_code' => 'zip_code',
			'value_zip_code' => '',
			'id_submit' => 'wp-submit'
		);

		$zip_code_checker_change_font_family = $args['zip_code_checker_change_family'];
		$zip_code_checker_change_color = $args['zip_code_checker_change_color'];
		$zip_code_checker_change_font_size = $args['zip_code_checker_change_font_size'];

		$zip_code_checker_style = 'font-family: '. $zip_code_checker_change_font_family .';';
		$zip_code_checker_style .= 'color: '. $zip_code_checker_change_color .';';
		$zip_code_checker_style .= 'font-size: '. $zip_code_checker_change_font_size .';';
		$style = 'style="'. $zip_code_checker_style . '"';

		$args = wp_parse_args( $args, $defaults );

		$form = '
			<form name="' . $args['form_id'] . '" id="' . $args['form_id'] . '" action="#" method="post" ' . $style . '>
				<p class="zip-code-checker">
					<input type="text" name="log" id="' . esc_attr( $args['id_zip_code'] ) . '" class="input" value="' . esc_attr( $args['value_zip_code'] ) . '" size="25" />
				</p>
				<p class="hidden">
					<input type="hidden" name="zip_code_checker_cod" id="zip_code_checker_cod" class="input" value="' . esc_attr( $instance['zip_code_checker_cod'] ) . '" size="25" />
				</p>
				<p class="hidden">
					<input type="hidden" name="zip_code_checker_display_company_name" id="zip_code_checker_display_company_name" class="input" value="' . esc_attr( $instance['zip_code_checker_display_company_name'] ) . '" size="25" />
				</p>
				<p class="zip-code-submit">
					<input type="submit" name="wp-submit" id="' . esc_attr( $args['id_submit'] ) . '" class="button-primary" value="' . esc_attr( $args['label_verify'] ) . '" />
				</p>
			</form>';

		echo $form;

		do_action( 'sidebar_zip_code_checker_widget_end' );

		echo $after_widget;
    }

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		$this->define_options();

		foreach ( $this->options as $name => $option ) {
			if ( $option['type'] == 'break' )
				continue;

			$instance[ $name ] = strip_tags( stripslashes( trim($new_instance[ $name ] )) );
		}
		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {
		$this->define_options();

		foreach ( $this->options as $name => $option ) {

			if ( $option['type'] == 'break' ) {
				echo '<hr style="border: 1px solid #ddd; margin: 1em 0" />';
				continue;
			}

			if ( ! isset( $instance[ $name ] ) )
				$instance[ $name ] = $option['default'];

			if ( empty( $option['placeholder'] ) )
				$option['placeholder'] = '';

			echo '<p>';

			switch ( $option['type'] ) {
				case "text" :
					?>
					<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><?php echo wp_kses_post( $option['label'] ) ?>:</label>
					<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" placeholder="<?php echo esc_attr( $option['placeholder'] ); ?>" value="<?php echo esc_attr( $instance[ $name ] ); ?>" />
					<?php
				break;
				case "checkbox" :
					?>
					<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" <?php checked( $instance[ $name ], 1 ) ?> value="1" /> <?php echo wp_kses_post( $option['label'] ) ?></label>
					<?php
				break;
				case "textarea" :
					?>
					<label for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><?php echo wp_kses_post( $option['label'] ) ?>:</label>
					<textarea class="widefat" cols="20" rows="3" id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" placeholder="<?php echo esc_attr( $option['placeholder'] ); ?>"><?php echo esc_textarea( $instance[ $name ] ); ?></textarea>
					<?php
				break;
			}

			if ( ! empty( $option['description'] ) )
				echo '<span class="description" style="display:block; padding-top:.25em">' . wp_kses_post( $option['description'] ) . '</span>';

			echo '</p>';
		}
	}
}

register_widget( 'Sidebar_Zip_Code_Checker_Widget' );