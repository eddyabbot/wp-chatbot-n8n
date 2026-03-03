<?php
/**
 * Plugin Name: WP ChatBot N8n
 * Plugin URI:  https://github.com/eddyabbot/wp-chatbot-n8n
 * Description: Integra il chatbot di n8n su tutto il sito WordPress. Configura l'URL del webhook e personalizza il widget direttamente dall'admin.
 * Version:     1.0.0
 * Author:      Edoardo Abate
 * License:     GPL-2.0-or-later
 * Text Domain: wp-chatbot-n8n
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPCN_VERSION', '1.0.0' );
define( 'WPCN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPCN_URL', plugin_dir_url( __FILE__ ) );

add_action( 'plugins_loaded', 'wpcn_init' );

function wpcn_init() {
	require_once WPCN_PATH . 'includes/class-settings.php';
	require_once WPCN_PATH . 'includes/class-admin.php';
	require_once WPCN_PATH . 'includes/class-frontend.php';

	new WPCN_Admin();
	new WPCN_Frontend();
}
