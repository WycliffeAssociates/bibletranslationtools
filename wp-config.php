<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */
 
define( 'WPMS_ON', true ); //Added for WP Mail SMTP
define( 'WPMS_SMTP_PASS', 't-6pre5PAjat' ); // SMTP Password
define('WP_CACHE', true); // Added by WP Rocket

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

$local_config = ABSPATH . 'local.config.php';
$use_local_config = false;
if (file_exists($local_config) && is_readable($local_config)) {
		include $local_config;
		$use_local_config = true;
}

//Using environment variables for DB connection information

$connectstr_dbhost = '';
$connectstr_dbname = '';
$connectstr_dbusername = '';
$connectstr_dbpassword = '';

// Decides whether to use local config file or $_SERVER variable to get database info
if ($use_local_config) {
		$connectstr_dbhost = $local_dbhost;
		$connectstr_dbname = $local_dbname;
		$connectstr_dbusername = $local_dbusername;
		$connectstr_dbpassword = $local_dbpassword;
} else {
		foreach ($_SERVER as $key => $value) {
		    if (strpos($key, "MYSQLCONNSTR_") !== 0) {
		        continue;
		    }

		    $connectstr_dbhost = preg_replace("/^.*Data Source=(.+?);.*$/", "\\1", $value);
		    $connectstr_dbname = preg_replace("/^.*Database=(.+?);.*$/", "\\1", $value);
		    $connectstr_dbusername = preg_replace("/^.*User Id=(.+?);.*$/", "\\1", $value);
		    $connectstr_dbpassword = preg_replace("/^.*Password=(.+?)$/", "\\1", $value);
		}
}

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', $connectstr_dbname);

/** MySQL database username */
define('DB_USER', $connectstr_dbusername);

/** MySQL database password */
define('DB_PASSWORD', $connectstr_dbpassword);

/** MySQL hostname */
define('DB_HOST', $connectstr_dbhost);

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/** SSL settings */
define( 'MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL | MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT );
define( 'MYSQL_SSL_CA', getenv('MYSQL_SSL_CA'));

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '|5*60W;*yLbX4+Nauh6pPrAQuf{,QZ,lDix_19Ne#Y^<]&@f/FtRoKAv&s<k-EY ');
define('SECURE_AUTH_KEY',  '@WrrwG5*>6*q4EzVsApEo5. A V9yA?^nY{A_xr&+WxLveaA>2[Dcf8Sd}N!o:3@');
define('LOGGED_IN_KEY',    '%J </Jg}aAtTvr_B|!q`GaQN;Pfg=:Sc|*ilm{s.:@fdG]8v8(*1l0}SNhRxM954');
define('NONCE_KEY',        'cBh+c3|AbGkn*mQ^*){-u#+.?-b*(Up[y`D|HH~a%/ ;)2=Ng<V(m)U<<feVLu8Q');
define('AUTH_SALT',        ']i} 4k{@-`Pd[Qz0-uT TE/DSxz22wG||%XE6D5C8lv@<6//.}s~Wj?tm0Po^_*a');
define('SECURE_AUTH_SALT', 'J;>.oEIc_|J2vkA^sE.dz4dTqgFXhm{O@*C#wbA_-C&L%.}FkVKZBjD+*iI[]h+g');
define('LOGGED_IN_SALT',   '^F7@JE]XF1CKa/_)i]g#eA.,cTU$q[3CTm8CpV,4V+zOz-, :{1}4#8I`?)0-_IZ');
define('NONCE_SALT',       '%vhaiZ}-aGJa^B=vL0(RKoDa,kP/qZf+2Enh$6A7:}uC<0NXQ?%*Y^3B<Au{L+T>');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
$debug = $use_local_config ? $local_debug : false;
define('WP_DEBUG', $debug);

/* That's all, stop editing! Happy blogging. */

//Relative URLs for swapping across app service deployment slots
define('WP_HOME', 'https://'. filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING));
define('WP_SITEURL', 'https://'. filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING));
define('WP_CONTENT_URL', 'https://'. filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING) . '/wp-content');
define('DOMAIN_CURRENT_SITE', filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING));


/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
