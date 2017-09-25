<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Kiwi_Social_Share_Shortcodes
 */
class Kiwi_Social_Share_Shortcodes {

	/**
	 * Kiwi_Social_Share_Shortcodes constructor.
	 *
	 * @param bool $advanced
	 *
	 */
	public function __construct( $advanced = false ) {
		if ( $advanced ) {
			/**
			 * Add a custom post type for our shortcodes
			 */
			add_action( 'init', array( $this, 'add_custom_post_type' ) );
			/**
			 * Add a metabox and register settings for the shortcodes
			 */
			add_action( 'cmb2_admin_init', array( $this, 'register_metabox' ) );
			/**
			 * Edit the columns from archive and add an identifier (easier to copy paste it where the user needs it)
			 */
			add_filter( 'manage_edit-kiwi-shortcodes_columns', array( $this, 'kiwi_shortcode_columns' ) );
			add_action( 'manage_kiwi-shortcodes_posts_custom_column', array( $this, 'kiwi_shortcode_column' ), 10, 2 );
		}

		add_shortcode( 'kiwi-social-bar', array( $this, 'kiwi_bar_shortcode' ) );
	}

	/**
	 * Register the custom post type
	 */
	public function add_custom_post_type() {
		register_post_type( 'kiwi-shortcodes',
		                    array(
			                    'labels'              => array(
				                    'name'               => esc_html__( 'Kiwi Shortcodes', 'kiwi-social-share' ),
				                    'singular_name'      => esc_html__( 'Kiwi Shortcode', 'kiwi-social-share' ),
				                    'not_found'          => esc_html__( 'No Kiwi Shortcodes found', 'kiwi-social-share' ),
				                    'not_found_in_trash' => esc_html__( 'No Kiwi Shortcodes found in trash', 'kiwi-social-share' )
			                    ),
			                    'menu_icon'           => 'dashicons-share-alt',
			                    'supports'            => array( 'title' ),
			                    'public'              => false,
			                    'exclude_from_search' => true,
			                    'show_ui'             => true,
			                    'show_in_menu'        => false,
			                    'has_archive'         => false
		                    )
		);
	}

	/**
	 * Customize the identifier column
	 *
	 * @param $column
	 * @param $post_id
	 *
	 */
	public function kiwi_shortcode_column( $column, $post_id ) {
		printf( '[kiwi-social-bar id="%s"]', $post_id );
	}

	/**
	 * Customize the kiwi shortcode columns
	 *
	 * @param $columns
	 *
	 * @return array
	 */
	public function kiwi_shortcode_columns( $columns ) {
		$columns = array(
			'cb'         => '<input type="checkbox" />',
			'title'      => __( 'Shortcode' ),
			'identifier' => __( 'Identifier' ),
			'date'       => __( 'Date' )
		);

		return $columns;
	}

	/**
	 * Register metaboxes using CMB2
	 */
	public function register_metabox() {
		/**
		 * Add the metabox
		 */
		$metabox = new_cmb2_box( array(
			                         'id'           => 'kiwi_shortcode_metabox',
			                         'title'        => esc_html__( 'Kiwi Shortcode Meta', 'kiwi-social-share' ),
			                         'object_types' => array( 'kiwi-shortcodes' ),
			                         'closed'       => false,
		                         ) );

		$id = '';
		if ( ! empty( $_GET ) && ! empty( $_GET['post'] ) ) {
			$id = $_GET['post'];
			/**
			 * Add shortcode fields
			 *
			 * This field is used as an identifier (user copies/pastes this content where he needs it)
			 */
			$metabox->add_field(
				array(
					'name'       => esc_html__( 'Shortcode list item style', 'kiwi-social-share' ),
					'id'         => 'kiwi_shortcode_identifier',
					'type'       => 'text',
					'default'    => '[kiwi-social-bar id="' . $id . '"]',
					'attributes' => array(
						'readonly' => 'readonly'
					)
				)
			);
		}


		/**
		 * Shortcode networks fields ( multicheck )
		 */
		$metabox->add_field(
			array(
				'name'    => esc_html__( 'Shortcode networks', 'kiwi-social-share' ),
				'id'      => 'kiwi_shortcode_networks',
				'type'    => 'multicheck',
				'options' => array(
					'facebook'    => esc_html__( 'Facebook', 'kiwi-social-share' ),
					'twitter'     => esc_html__( 'Twitter', 'kiwi-social-share' ),
					'google-plus' => esc_html__( 'Google Plus', 'kiwi-social-share' ),
					'linkedin'    => esc_html__( 'LinkedIn', 'kiwi-social-share' ),
					'pinterest'   => esc_html__( 'Pinterest', 'kiwi-social-share' ),
				),
			)
		);

		/**
		 * Shortcode bar style
		 */
		$metabox->add_field(
			array(
				'name'    => esc_html__( 'Shortcode bar style', 'kiwi-social-share' ),
				'id'      => 'kiwi_shortcode_bar_style',
				'type'    => 'radio',
				'default' => 'fit',
				'options' => array(
					'fit'    => esc_html__( 'Fit', 'kiwi-social-share' ),
					'center' => esc_html__( 'Center', 'kiwi-social-share' ),
				),
			)
		);

		/**
		 * Shortcode list item styles
		 */
		$metabox->add_field(
			array(
				'name'    => esc_html__( 'Shortcode list item style', 'kiwi-social-share' ),
				'id'      => 'kiwi_shortcode_list_item_style',
				'type'    => 'radio',
				'default' => 'rect',
				'options' => array(
					'rect'  => esc_html__( 'Rectangular', 'kiwi-social-share' ),
					'leaf'  => esc_html__( 'Leaf', 'kiwi-social-share' ),
					'shift' => esc_html__( 'Shift', 'kiwi-social-share' ),
					'pill'  => esc_html__( 'Pill', 'kiwi-social-share' ),
				),
			)
		);
	}

	/**
	 * @param null $atts
	 * @param null $content
	 *
	 * @return mixed|null|string
	 */
	public function kiwi_bar_shortcode( $atts = NULL, $content = NULL ) {
		$instance = array(
			'networks' => Kiwi_Social_Share_Helper::get_setting_value( 'networks_article_bar', array() ),
			'style'    => Kiwi_Social_Share_Helper::get_setting_value( 'article_bar_style', 'center' ),
			'items'    => Kiwi_Social_Share_Helper::get_setting_value( 'button_shape', 'rect' )
		);

		if ( ! empty( $atts ) ) {
			$instance = array(
				'networks' => get_post_meta( $atts['id'], 'kiwi_shortcode_networks', true ),
				'style'    => get_post_meta( $atts['id'], 'kiwi_shortcode_bar_style', true ),
				'items'    => get_post_meta( $atts['id'], 'kiwi_shortcode_list_item_style', true )
			);
		}

		$defaults = array(
			'networks' => array(),
			'style'    => 'center',
			'items'    => 'rect'
		);


		$instance = wp_parse_args( $instance, $defaults );
		$bar      = new Kiwi_Social_Share_View_Shortcode_Bar( $instance['networks'], $instance['style'], $instance['items'] );

		return $bar->generate_frontend_bar();
	}

	/**
	 * @param null $atts
	 * @param null $content
	 *
	 * @return mixed|null|string
	 */
	public function kiwi_bar_simple( $atts = NULL, $content = NULL ) {
		$defaults = array(
			'networks' => Kiwi_Social_Share_Helper::get_setting_value( 'networks_article_bar', array() ),
			'style'    => Kiwi_Social_Share_Helper::get_setting_value( 'article_bar_style', 'center' ),
			'items'    => Kiwi_Social_Share_Helper::get_setting_value( 'button_shape', 'rect' )
		);

		$bar = new Kiwi_Social_Share_View_Shortcode_Bar( $defaults['networks'], $defaults['style'], $defaults['items'] );

		return $bar->generate_frontend_bar();
	}
}