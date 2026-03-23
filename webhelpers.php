<?php
/**
 * Plugin Name: EWEB Core WebHelpers Pro
 * Description: Professional utility suite for WordPress. Modular collection of performance, security, and development helpers. Includes support for WPML, ACF, and automated GitHub updates. Part of the EWEB Plugin Suite.
 * Version: 1.2.0
 * Author: Yisus Develop
 * Author URI: https://github.com/Yisus-Develop
 * Plugin URI: https://enlaweb.co/
 * License: GPL v2 or later
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Text Domain: eweb-webhelpers-pro
 * Domain Path: /languages
 * 
 * EWEB Core WebHelpers Pro - Developed by Yisus Develop
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'WEBHELPERS_VERSION', '1.2.0' );
define( 'WEBHELPERS_PATH', plugin_dir_path( __FILE__ ) );
define( 'WEBHELPERS_URL',  plugin_dir_url( __FILE__ ) );

add_action( 'plugins_loaded', function() {
    load_plugin_textdomain( 'eweb-webhelpers-pro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
});

require_once WEBHELPERS_PATH . 'includes/routes.php';

// Inicializar el sistema de actualizaciones desde GitHub (Professional Edition)
if ( is_admin() ) {
    $updater_file = WEBHELPERS_PATH . 'includes/class-eweb-github-updater.php';
    if ( file_exists( $updater_file ) ) {
        require_once $updater_file;
        new EWEB_GitHub_Updater( __FILE__, 'Yisus-Develop', 'eweb-webhelpers-pro' );
    }
}





