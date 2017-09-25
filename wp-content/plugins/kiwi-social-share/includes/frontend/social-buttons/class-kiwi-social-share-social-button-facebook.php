<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Kiwi_Social_Share_Social_Button_Facebook
 */
final class Kiwi_Social_Share_Social_Button_Facebook extends Kiwi_Social_Share_Social_Button implements Kiwi_Social_Share_Interface_Social {
	/**
	 * Kiwi_Social_Share_Social_Button_Facebook constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->platform = 'facebook';
		$this->url      = $this->build_url();

		$this->api_url = 'http://graph.facebook.com/?id=' . rawurlencode( $this->get_current_page_url( $this->post_id ) );
	}

	/**
	 * @return string
	 */
	public function build_url() {
		$url = $this->get_current_page_url( $this->post_id );


		return '//www.facebook.com/sharer.php?u=' . rawurlencode( $url );
	}

	/**
	 * @return string
	 */
	public function generate_output() {
		return '<a data-class="popup" data-network="' . esc_attr( $this->platform ) . '" class="' . esc_attr( $this->generate_anchor_class() ) . '" href="' . esc_url( $this->url ) . '" target="_blank" rel="nofollow">' . $this->generate_anchor_icon() . ' ' . $this->build_shared_count() . '</a>';
	}

	/**
	 * @param $response
	 *
	 * @return bool
	 */
	public function parse_api_response( $response ) {
		$response = json_decode( $response['body'], true );

		if ( empty( $response['share'] ) ) {
			return false;
		}

		return $response['share']['share_count'];
	}
}