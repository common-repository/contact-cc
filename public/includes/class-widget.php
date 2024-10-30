<?php
use ChopChop\ContactFree;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Contact_CC_Pro_Widget extends WP_Widget {

	protected $plugin_slug = 'chch-contact-free';
	protected $plugin_name = 'Contact CC Free';

	function __construct() {
		parent::__construct(
			$this->plugin_slug,
			__( $this->plugin_name, $this->plugin_name ), 
			array( 'description' => __( $this->plugin_name, $this->plugin_name ), )
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
		echo $args['before_widget'];
		echo ChopChop\ContactFree\Contact_Free_Main::show_contacts( $instance[ 'form_id'] );
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
		$form_id = ! empty( $instance['form_id'] ) ? $instance['form_id'] : __( 'New form_id', 'text_domain' );
		?>
		<p> 

		<label for="<?php echo $this->get_field_id( 'form_id' ); ?>"><?php _e( 'Contact Form:', $this->plugin_name); ?></label>
		<?php $args = array(
			'posts_per_page'   => -1,
			'post_type'        => $this->plugin_slug,
		);
		$forms_array = get_posts( $args );

		if (is_array($forms_array) && count($forms_array)) :
		?>
			<select id="<?php echo $this->get_field_id( 'form_id' ); ?>" name="<?php echo $this->get_field_name( 'form_id' ); ?>">
				<?php foreach ($forms_array as $form) :
				$selected = (esc_attr( $form_id ) == $form->ID) ? ' selected' : '';
				?>
					<option value="<?php echo $form->ID; ?>"<?php echo $selected; ?>><?php echo $form->post_title ? $form->post_title : 'Contact Form '.$form->ID; ?></option>
				<?php endforeach ?>
			</select>
		</p>
		<?php 
		endif;
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
		$instance['form_id'] = ( ! empty( $new_instance['form_id'] ) ) ? strip_tags( $new_instance['form_id'] ) : '';

		return $instance;
	}

}

function chch_contact_free_register_widget() {
    register_widget( 'Contact_CC_Pro_Widget' );
}
add_action( 'widgets_init', 'chch_contact_free_register_widget' );