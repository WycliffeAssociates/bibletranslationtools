<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Kiwi_Social_Share_Social_Button_Google_Plus
 */
final class Kiwi_Social_Share_Social_Button_Google_Plus extends Kiwi_Social_Share_Social_Button implements Kiwi_Social_Share_Interface_Social {
	/**
	 * Kiwi_Social_Share_Social_Button_Google_Plus constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->platform = 'google-plus';
		$this->url      = $this->build_url();
		$this->api_url  = 'https://clients6.google.com/rpc';
	}

	/**
	 * @return string
	 */
	public function build_url() {
		$url = $this->get_current_page_url( $this->post_id );
		return '//plus.google.com/share?url=' . rawurlencode( $url );
	}

	/**
	 * @return string
	 */
	public function generate_output() {
		return '<a data-class="popup" data-network="' . esc_attr( $this->platform ) . '" class="' . esc_attr( $this->generate_anchor_class() ) . '" href="' . esc_url( $this->url ) . '" target="_blank" rel="nofollow">' . $this->generate_anchor_icon() . ' ' . $this->build_shared_count() . '</a>';
	}

	/**
	 * @return bool
	 */
	public function connect_to_api_url() {
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, $this->api_url );
		curl_setopt( $curl, CURLOPT_POST, 1 );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . rawurlencode( get_the_permalink( get_the_ID() ) ) . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]' );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) );
		$curl_results = curl_exec( $curl );
		curl_close( $curl );

		$json = json_decode( $curl_results, true );

		return $this->parse_api_response( $json );
	}

	/**
	 * @param $response
	 *
	 * @return bool
	 */
	public function parse_api_response( $response ) {
		if ( ! isset( $response[0]['result'] ) ) {
			return false;
		}

		return intval( $response[0]['result']['metadata']['globalCounts']['count'] );
	}
}