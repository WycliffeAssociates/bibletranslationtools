<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Kiwi_Social_Share_Metaboxes
 */
class Kiwi_Social_Share_Metaboxes {
	/**
	 * @var object
	 *
	 * CMB2 object
	 */
	public $metabox = NULL;
	/**
	 * @var array
	 */
	public $options = array();
	/**
	 * @var bool
	 */
	public $global = false;
	/**
	 * @var array
	 */
	public $post_types = array();

	/**
	 * Kiwi_Social_Share_Metaboxes constructor.
	 */
	public function __construct() {
		$this->prefix = 'kiwi_';

		$this->check_if_global();
		$this->check_post_types();

		$this->set_options();

		add_action( 'cmb2_admin_init', array( $this, 'register_metabox' ) );
	}

	/**
	 * Set the options that we will add to the metabox
	 */
	public function set_options() {
		$this->options = array(
			'social-media-title' => array(
				'name' => esc_html__( 'Social Media Title', 'kiwi-social-share' ),
				'id'   => $this->prefix . 'social-media-title',
				'type' => 'text',
			),

			'social-media-description' => array(
				'name' => esc_html__( 'Social Media Description', 'kiwi-social-share' ),
				'id'   => $this->prefix . 'social-media-description',
				'type' => 'textarea',
			),

			'social-media-image' => array(
				'name' => esc_html__( 'Social Media Image', 'kiwi-social-share' ),
				'id'   => $this->prefix . 'social-media-image',
				'type' => 'file',
			),

			'social-media-custom-tweet' => array(
				'name' => esc_html__( 'Custom Tweet', 'kiwi-social-share' ),
				'id'   => $this->prefix . 'social-media-custom-tweet',
				'type' => 'textarea',
			),

			'social-media-pinterest-image' => array(
				'name' => esc_html__( 'Social Media Pinterest Image', 'kiwi-social-share' ),
				'id'   => $this->prefix . 'social-media-pinterest-image',
				'type' => 'file',
			),

			'social-media-pinterest-description' => array(
				'name' => esc_html__( 'Pinterest Description', 'kiwi-social-share' ),
				'id'   => $this->prefix . 'social-media-pinterest-description',
				'type' => 'textarea',
			),
		);
	}

	/**
	 * In case we have an 'all' match, we don`t need to search for specific post types
	 */
	public function check_if_global() {
		$metaboxes = Kiwi_Social_Share_Helper::get_setting_value( 'custom_meta_boxes_posttypes', '' );
		if ( 'all' == $metaboxes ) {
			$this->global = true;
		}
	}

	/**
	 * Get all the post types where we need to add the custom metaboxes
	 */
	public function check_post_types() {
		$all_post_types = Kiwi_Social_Share_Helper::get_custom_post_types();
		foreach ( $all_post_types as $k => $v ) {
			$this->post_types[] = $k;
		}

		if ( ! $this->global ) {
			$this->post_types = Kiwi_Social_Share_Helper::get_setting_value( 'custom_meta_boxes_posttypes_list', array() );
		}
	}

	/**
	 * Register metaboxes
	 */
	public function register_metabox() {
		$this->metabox = new_cmb2_box( array(
			                               'id'           => $this->prefix . 'metabox',
			                               'title'        => esc_html__( 'Kiwi Social Share Meta Information', 'kiwi-social-share' ),
			                               'object_types' => $this->post_types,
			                               'closed'       => false,
		                               ) );

		$this->add_fields();
	}

	/**
	 * Add fields to the metabox
	 */
	public function add_fields() {
		foreach ( $this->options as $id => $option ) {
			$this->metabox->add_field( $option );
		}
	}
}