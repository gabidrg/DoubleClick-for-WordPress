<?php

// Get our global varaible.
global $DoubleClick;

/**
 * Adds DoubleClick_Widget widget.
 */
class DoubleClick_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'doubleclick_widget', // Base ID
			__( 'DoubleClick Ad', 'dfw' ), // Name
			array( 'description' => __( 'Serve ads from DFP.', 'dfw' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		global $DoubleClick;
		
		echo $args['before_widget'];

    	$width = ! empty( $instance['width'] ) ? $instance['width'] : '300';
		$height = ! empty( $instance['height'] ) ? $instance['height'] : '250';
    	$identifier = ! empty( $instance['identifier'] ) ? $instance['identifier'] : 'ident';
    	$size = $width . "x" . $height;

    	$breakpoints = $instance['breakpoints'];

    	$DoubleClick->place_ad($identifier,$size,$breakpoints);

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		global $DoubleClick;

		$identifier = ! empty( $instance['identifier'] ) ? $instance['identifier'] : "";
		$width = ! empty( $instance['width'] ) ? $instance['width'] : '300';
		$height = ! empty( $instance['height'] ) ? $instance['height'] : '250';

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'identifier' ); ?>"><?php _e( 'Identifier:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'identifier' ); ?>" name="<?php echo $this->get_field_name( 'identifier' ); ?>" type="text" value="<?php echo esc_attr( $identifier ); ?>">
		</p>	

		<p>
			<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo esc_attr( $width ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo esc_attr( $height ); ?>">
		</p>

		<p><strong>Show for breakpoints:</strong></p>

		<?php 
			$selectedBreakpoints = $instance['breakpoints']; 
		?>

		<?php if( sizeof($DoubleClick->breakpoints) > 0 ) : ?>

			<p style="margin:15px 0 15px 5px;">

			<?php foreach($DoubleClick->breakpoints as $b) : ?>
				<input 
					class="checkbox" 
					style="margin-right:8px;" 
					type="checkbox" 
					name="<?php echo $this->get_field_name( 'breakpoints' ); ?>[]" 
					value="<?php echo $b->identifier; ?>" 
					<?php if( in_array($b->identifier, $selectedBreakpoints ) ) echo "checked"; ?>
					/>
				<label><?php echo $b->identifier; ?></label><br/>
			<?php endforeach; ?>

			</p>

		<?php else : ?>

			<p style='margin-top:-14px;'><em>No breakpoints defined.</em></p>

		<?php endif; ?>

		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();

		$instance['identifier'] = ( ! empty( $new_instance['identifier'] ) ) ? strip_tags( $new_instance['identifier'] ) : '';
		$instance['width'] = ( ! empty( $new_instance['width'] ) ) ? strip_tags( $new_instance['width'] ) : '300';
		$instance['height'] = ( ! empty( $new_instance['height'] ) ) ? strip_tags( $new_instance['height'] ) : '250';
		$instance['breakpoints'] = $new_instance['breakpoints'];

		return $instance;
	}

}

function dfw_register_widget() {

	register_widget( 'DoubleClick_Widget' );
}

add_action( 'widgets_init', 'dfw_register_widget');

