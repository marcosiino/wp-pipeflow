<?php
/**
 * Plugin Name: PagineDaColorare.it AI Bot
 * Plugin URI: https://paginedacolorare.it
 * Description: Questo plugin genera disegni da colorare per il sito utilizzando l'intelligenza artificiale.
 * Version: 1.0
 * Author: Marco Siino
 * Author URI: http://marcosiino.it
 */
// Prevenire l'accesso diretto al file del plugin.
defined('ABSPATH') or die('Accesso non permesso.');

define('PLUGIN_PATH', plugin_dir_path(__FILE__));

require_once(PLUGIN_PATH . 'utils/utils.php');
require_once(PLUGIN_PATH . 'utils/cronjobs.php');
require_once(PLUGIN_PATH . 'utils/http_requests_timeout_settings.php');

require_once(PLUGIN_PATH . 'admin-pages/general-settings.php');
require_once(PLUGIN_PATH . 'admin-pages/manual-generation.php');

setup_http_requests();
setup_cronjobs();

/**
 * Plugin activation
 */
function activation() {
    // Schedule the cronjob
    schedule_cronjobs();
}
register_activation_hook(__FILE__, 'activation');

/**
 * Plugin deactivation
 */
function deactivation() {
    //Unschedule the wp cron
    unschedule_cronjobs();
}
register_deactivation_hook(__FILE__, 'deactivation');

/**
 * Plugin initialization
 */
function init() {
    // Initialize your plugin here
}
add_action('init', 'init');


/**
 * Register the plugin settings
 */
function register_plugin_settings() {
    register_setting('paginedacolorare_ai_options_group', 'paginedacolorare_ai_openai_api_key');
}
add_action('admin_init', 'register_plugin_settings');

/**
 * Setups the plugin admin menu
 */
function setup_admin_menu() {
    add_menu_page(
        'General Settings', // Titolo della pagina
        'AI Bot', // Titolo del menu
        'manage_options', // Capability
        'paginedacolorare-ai-general-settings', // Slug del menu
        'general_plugin_settings', // Funzione per visualizzare la pagina di impostazioni
        'dashicons-admin-customizer', // Icona del menu
        6 // Posizione nel menu
    );

    // Aggiunge la stessa pagina del menu principale come sottovoce
    add_submenu_page(
        'paginedacolorare-ai-general-settings', // Slug del menu principale
        'Generate', // Titolo della pagina
        'Generate', // Titolo del menu
        'manage_options', // Capability
        'paginedacolorare-ai-manual-generation', // Slug della pagina (deve corrispondere allo slug del menu principale per questa sottovoce)
        'manual_generation_admin_page' // Funzione per il contenuto della pagina
    );
}
add_action('admin_menu', 'setup_admin_menu');
