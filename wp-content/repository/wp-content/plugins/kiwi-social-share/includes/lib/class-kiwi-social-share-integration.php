<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Kiwi_Social_Share_Integration
 */
class Kiwi_Social_Share_Integration {
	/**
	 * @var array
	 */
	public $themes = array();
	/**
	 * @var array
	 */
	public $plugins = array();
	/**
	 * @var array
	 */
	public $strings = array();

	/**
	 * Kiwi_Social_Share_Integration constructor.
	 */
	public function __construct() {
		$this->themes = array(
			'newsmag'     => array(
				'slug'   => 'newsmag',
				'label'  => esc_html__( 'NewsMag Lite', 'kiwi-social-share' ),
				'author' => esc_html__( 'MachoThemes', 'kiwi-social-share' )
			),
			'regina-lite' => array(
				'slug'   => 'regina-lite',
				'label'  => esc_html__( 'Regina Lite', 'kiwi-social-share' ),
				'author' => esc_html__( 'MachoThemes', 'kiwi-social-share' )
			),
			'affluent'    => array(
				'slug'   => 'affluent',
				'label'  => esc_html__( 'Affluent', 'kiwi-social-share' ),
				'author' => esc_html__( 'CPO Themes', 'kiwi-social-share' )
			),
			'allegiant'   => array(
				'slug'   => 'allegiant',
				'label'  => esc_html__( 'Allegiant', 'kiwi-social-share' ),
				'author' => esc_html__( 'CPO Themes', 'kiwi-social-share' )
			),
			'transcend'   => array(
				'slug'   => 'transcend',
				'label'  => esc_html__( 'Transcend', 'kiwi-social-share' ),
				'author' => esc_html__( 'CPO Themes', 'kiwi-social-share' )
			),
		);

		$this->strings = array(
			'not-installed' => esc_html__( 'Install Now', 'kiwi-social-share' ),
			'not-active'    => esc_html__( 'Activate', 'kiwi-social-share' ),
		);

		$this->class_names = array(
			'not-installed' => 'theme-install',
			'not-active'    => 'activate'
		);

		$this->call_themes_api();
	}

	/**
	 * @return array
	 */
	public function call_themes_api() {
		include_once( ABSPATH . 'wp-admin/includes/theme-install.php' );

		$themes = array();
		foreach ( $this->themes as $theme ) {
			$themes[ $theme['slug'] ] = $this->_call_themes_api( $theme['slug'] );
		}

		foreach ( $themes as $k => $v ) {
			$this->themes[ $k ]['version']        = $v->version;
			$this->themes[ $k ]['preview_url']    = $v->preview_url;
			$this->themes[ $k ]['screenshot_url'] = $v->screenshot_url;
			$this->themes[ $k ]['homepage']       = $v->homepage;
			$this->themes[ $k ]['download_link']  = $v->download_link;
			$this->themes[ $k ]['status']         = $this->set_status( $v->slug, 'theme' );
			$this->themes[ $k ]['action_link']    = $this->create_action_link( $v->slug, 'theme' );
		}

		return $themes;

	}

	/**
	 * @param $slug
	 *
	 * @return array|mixed|object|WP_Error
	 */
	private function _call_themes_api( $slug ) {

		if ( false === ( $call_api = get_transient( 'kiwi_themes_information_transient_' . $slug ) ) ) {
			$call_api = themes_api( 'theme_information', array(
				'slug' => $slug,
			) );
			set_transient( 'kiwi_themes_information_transient_' . $slug, $call_api, 30 * MINUTE_IN_SECONDS );
		}

		return $call_api;
	}

	/**
	 * @param $slug
	 * @param $context
	 *
	 * @return bool|string
	 */
	private function set_status( $slug, $context ) {
		switch ( $context ) {
			case 'theme':
				$t = wp_get_theme( $slug );
				if ( is_wp_error( $t->errors() ) ) {
					return 'not-installed';
				}

				$t = wp_get_theme();

				if ( $slug == $t->name || $slug == $t->parent_theme ) {
					return 'active';
				}

				return 'not-active';
				break;
			default:
				return false;
				break;
		}
	}

	/**
	 * @param $slug
	 * @param $context
	 *
	 * @return bool|string
	 */
	private function create_action_link( $slug, $context ) {
		switch ( $context ) {
			case 'theme':
				$status = $this->set_status( $slug, $context );
				switch ( $status ) {
					case 'not-installed':
						return wp_nonce_url(
							add_query_arg(
								array(
									'action' => 'install-theme',
									'theme'  => $slug
								),
								network_admin_url( 'update.php' )
							),
							'install-theme_' . $slug
						);
						break;
					case 'not-active':
						return wp_nonce_url(
							add_query_arg(
								array(
									'action'     => 'activate',
									'stylesheet' => $slug
								),
								network_admin_url( 'themes.php' )
							),
							'switch-theme_' . $slug
						);
						break;
					default :
						return '';
						break;
				}


				break;
			default:
				return false;
				break;
		}

	}
}