<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Kiwi_Social_Share_Social_Button
 */
abstract class Kiwi_Social_Share_Social_Button {
	/**
	 * @var
	 */
	public $platform;
	/**
	 * @var
	 */
	public $url;
	/**
	 * @var
	 */
	public $api_url;
	/**
	 * @var
	 */
	public $settings;
	/**
	 * @var string
	 */
	public $post_id;


	/**
	 * Kiwi_Social_Share_Social_Button constructor.
	 */
	public function __construct() {
		$this->post_id = get_the_ID();
		if ( is_front_page() || is_archive() || is_date() || is_category() || is_home() ) {
			$this->post_id = 'fp';
		}
	}

	/**
	 * @param $id
	 *
	 * @return mixed|string
	 */
	public function get_excerpt_by_id( $id ) {
		return Kiwi_Social_Share_Helper::get_excerpt_by_id( $id );
	}

	/**
	 * @return string
	 */
	public function generate_anchor_class() {
		return 'kiwi-nw-' . $this->platform;
	}

	/**
	 * @param string $custom
	 *
	 * @return string
	 */
	public function generate_anchor_icon( $custom = '' ) {
		if ( ! empty( $custom ) ) {
			$this->platform = $custom;
		}

		return '<span class="kicon-' . esc_attr( $this->platform ) . '"></span>';
	}


	/**
	 * Returns an int, containing the number of shares
	 */
	public function get_shared_count() {
		$response = get_transient( 'kiwi_' . $this->post_id . '_share_count_transient' );
		if ( false === $response || empty( $response[ $this->platform ] ) ) {
			$api = $this->connect_to_api_url();

			if ( ! $api ) {
				$api = 0;
			}

			$response[ $this->platform ] = $api;
			/**
			 * Add a transient available 2 HOURS
			 */
			set_transient( 'kiwi_' . $this->post_id . '_share_count_transient', $response, 2 * HOUR_IN_SECONDS );
		}

		return $response[ $this->platform ];
	}

	/**
	 * Returns a string, HTML code
	 */
	public function build_shared_count() {
		if ( $this->get_shared_count() > 0 && Kiwi_Social_Share_Helper::get_setting_value( 'share_counts', '' ) === 'on' ) {
			return '<span class="kiwi-share-count">' . $this->get_shared_count() . '</span>';
		};

		return '';
	}

	/**
	 * @return bool|mixed
	 */
	public function connect_to_api_url() {
		if ( empty( $this->api_url ) ) {
			return false;
		}

		$response = wp_remote_get( $this->api_url );

		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		if ( empty( $response['body'] ) ) {
			return false;
		}

		return $this->parse_api_response( $response );
	}

	/**
	 * @param $response
	 *
	 * @return mixed
	 */
	public function parse_api_response( $response ) {
		return 0;
	}

	/**
	 * @param $id
	 *
	 * @return false|string
	 */
	public function get_current_page_url( $id ) {
		if ( $id === 'fp' ) {
			return get_home_url();
		}

		return get_the_permalink( $id );
	}
}