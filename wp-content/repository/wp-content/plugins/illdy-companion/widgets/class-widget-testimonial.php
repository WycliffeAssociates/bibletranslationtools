<?php
class Illdy_Widget_Testimonial extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct( 'illdy_testimonial', __( '[Illdy] - Testimonial', 'illdy' ), array( 'description' => __( 'Add this widget in "Front page - Testimonial Sidebar".', 'illdy' ), ) );

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    /**
     *  Enqueue Scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_media();
        wp_enqueue_script( 'illdy-widget-upload-image', get_template_directory_uri() . '/layout/js/widget-upload-image/widget-upload-image.js', false, '1.0', true);
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

        $lightbox = get_theme_mod( 'illdy_projects_lightbox', false );

        $name = ( !empty( $instance['name'] ) ? esc_html( $instance['name'] ) : '' );
        $image = !empty( $instance['image'] ) ? esc_url( $instance['image'] ) : '';
        $testimonial = !empty( $instance['testimonial'] ) ? sanitize_text_field( $instance['testimonial'] ) : '';

        $image_id = illdy_get_image_id_from_image_url( $image );
        $get_attachment_image_src = wp_get_attachment_image_src( $image_id, 'illdy-front-page-projects' );

        ?>

        <div class="carousel-testimonial">
            <div class="testimonial-image">
                <img src="<?php echo $image_id ? esc_url( $get_attachment_image_src[0] ) : $image ?>">
            </div><!--/.testimonial-image-->
            <div class="testimonial-content">
                <blockquote><q><?php echo $testimonial; ?></q></blockquote>
            </div><!--/.testimonial-content-->
            <div class="testimonial-meta">
                <h6><?php echo $name; ?></h6>
            </div><!--/.testimonial-meta-->
        </div><!--/.carousel-testimonial-->


        <?php

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
        $name = !empty( $instance['name'] ) ? sanitize_text_field( $instance['name'] ) : '';
        $image = !empty( $instance['image'] ) ? esc_url( $instance['image'] ) : '';
        $testimonial = !empty( $instance['testimonial'] ) ? sanitize_text_field( $instance['testimonial'] ) : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'name' ); ?>"><?php _e( 'Name:', 'illdy' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>" type="text" value="<?php echo esc_attr( $name ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_name( 'image' ); ?>"><?php _e( 'Image:', 'illdy' ); ?></label>
            <input type="text" class="widefat custom_media_url_<?php echo $this->get_field_id( 'image' ); ?>" name="<?php echo $this->get_field_name( 'image' ); ?>" id="<?php echo $this->get_field_id( 'image' ); ?>" value="<?php if( !empty( $instance['image'] ) ): echo $instance['image']; else: get_template_directory_uri() . '/layout/images/front-page/front-page-project-1.jpg'; endif; ?>" style="margin-top:5px;">
            <input type="button" class="button button-primary custom_media_button" id="custom_media_button_service" data-fieldid="<?php echo $this->get_field_id( 'image' ); ?>" name="<?php echo $this->get_field_name( 'image' ); ?>" value="<?php _e( 'Upload Image','illdy' ); ?>" style="margin-top: 5px;">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'testimonial' ); ?>"><?php _e( 'Testimonial:', 'illdy' ); ?></label>
            <textarea class="widefat" id="<?php echo $this->get_field_id( 'testimonial' ); ?>" name="<?php echo $this->get_field_name( 'testimonial' ); ?>">
                    <?php echo esc_attr( $testimonial ); ?>
            </textarea>
        </p>
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
        $instance['name'] = ( !empty( $new_instance['name'] ) ) ? esc_html( $new_instance['name'] ) : '';
        $instance['image'] = !empty( $new_instance['image'] ) ? esc_url( $new_instance['image'] ) : '';
        $instance['testimonial'] = ( !empty( $new_instance['testimonial'] ) ? esc_html( $new_instance['testimonial'] ) : '' );

        return $instance;
    }

}

function illdy_register_widget_testimonial () {
    register_widget( 'Illdy_Widget_Testimonial' );
}
add_action( 'widgets_init', 'illdy_register_widget_testimonial' );