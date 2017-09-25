<?php
/*
 * Plugin Name: Kiwi Social Share - Social Media Share Buttons & Icons
 * Version: 2.0.4
 * Plugin URI: https://www.machothemes.com/kiwi-social-share
 * Description: Really beautiful & simple social media & share buttons + icons. Simplicity & speed is key with this social media share plugin.
 * Author: Macho Themes
 * Author URI: https://www.machothemes.com
 * Requires at least: 4.0
 * Tested up to: 4.7
 *
 * Text Domain: kiwi-social-share
 * Domain Path: /languages/
 *
 * @package WordPress
 * @author Macho Themes
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KIWI_SOCIAL_SHARE_BASE', plugin_dir_path( __FILE__ ) );
define( 'KIWI_SOCIAL_SHARE_URL', plugin_dir_url( __FILE__ ) );
define( 'KIWI_SOCIAL_SHARE_SITE', rtrim(ABSPATH, '\\/') );

// Load plugin class files
require_once 'includes/class-kiwi-social-share.php';
require_once 'includes/lib/helpers/class-kiwi-social-share-helper.php';

require_once 'includes/class-kiwi-social-share-autoloader.php';

/**
 * Returns the main instance of Kiwi_Social_Share to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Kiwi_Social_Share
 */
function Kiwi_Social_Share() {
	$instance = Kiwi_Social_Share::instance( __FILE__, '2.0.4' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Kiwi_Social_Share_Settings::instance( $instance );
	}

	return $instance;
}

Kiwi_Social_Share();