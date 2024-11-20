<?php
/**
 * Plugin Name: WP PipeFlow
 * Plugin URI: https://marcosiino.it
 * Description: Automatic post generator using the AI.
 * Version: 1.0
 * Author: Marco Siino
 * Author URI: http://marcosiino.it
 */

use Pipeline\StagesRegistration;

defined('ABSPATH') or die('Accesso non permesso.');

define('WP_PIPEFLOW_PLUGIN_PATH', plugin_dir_path(__FILE__));

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StagesRegistration.php";

require_once(ABSPATH . 'wp-content/plugins/wp-pipeflow/utils/utils.php');
require_once(ABSPATH . 'wp-content/plugins/wp-pipeflow/utils/cronjobs.php');
require_once(ABSPATH . 'wp-content/plugins/wp-pipeflow/utils/http_requests_timeout_settings.php');

require_once(ABSPATH . 'wp-content/plugins/wp-pipeflow/admin-pages/general-settings.php');
require_once(ABSPATH . 'wp-content/plugins/wp-pipeflow/admin-pages/pipeline-configuration.php');
require_once(ABSPATH . 'wp-content/plugins/wp-pipeflow/admin-pages/content-generation-settings.php');
require_once(ABSPATH . 'wp-content/plugins/wp-pipeflow/admin-pages/automatic-generation-settings.php');
require_once(ABSPATH . 'wp-content/plugins/wp-pipeflow/admin-pages/manual-generation.php');

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
    register_general_settings();
    register_pipeline_configuration_setup_settings();
    register_content_generation_settings();
    register_automatic_generation_settings();
}
add_action('admin_init', 'register_plugin_settings');


/**
 * Registers all the available stages for the Generation Pipeline
 */
function register_pipeline_stages_factories() {
    StagesRegistration::registerStages();
}
add_action('plugins_loaded', 'register_pipeline_stages_factories');
/**
 * Setups the plugin admin menu
 */
function setup_admin_menu() {
    // Main Menu / General Settings
    add_menu_page(
        'General Settings', // Titolo della pagina
        'PostBrewer', // Titolo del menu
        'manage_options', // Capability
        'postbrewer', // Slug del menu
        'general_plugin_settings', // Funzione per visualizzare la pagina di impostazioni
        'dashicons-admin-customizer', // Icona del menu
        6 // Posizione nel menu
    );

    // Content Generation Settings Menu Item
    add_submenu_page(
        'postbrewer', // Slug del menu principale
        'Pipeline Configuration', // Titolo della pagina
        'Pipeline Configuration', // Titolo del menu
        'manage_options', // Capability
        'postbrewer-pipeline-configuration', // Slug della pagina (deve corrispondere allo slug del menu principale per questa sottovoce)
        'pipeline_configuration_page' // Funzione per il contenuto della pagina
    );

    // Content Generation Settings Menu Item
    add_submenu_page(
        'postbrewer', // Slug del menu principale
        'Content Generation Settings', // Titolo della pagina
        'Content Generation Settings', // Titolo del menu
        'manage_options', // Capability
        'postbrewer-content-generation-settings', // Slug della pagina (deve corrispondere allo slug del menu principale per questa sottovoce)
        'content_generation_settings_page' // Funzione per il contenuto della pagina
    );

    // Automatic Generation Settings Menu Item
    add_submenu_page(
        'postbrewer', // Slug del menu principale
        'Auto Generation Settings', // Titolo della pagina
        'Auto Generation Settings', // Titolo del menu
        'manage_options', // Capability
        'postbrewer-auto-generation-settings', // Slug della pagina (deve corrispondere allo slug del menu principale per questa sottovoce)
        'automatic_generation_settings_page' // Funzione per il contenuto della pagina
    );

    // Manual Content Generation Menu Item
    add_submenu_page(
        'postbrewer', // Slug del menu principale
        'Generate', // Titolo della pagina
        'Generate', // Titolo del menu
        'manage_options', // Capability
        'postbrewer-manual-generation', // Slug della pagina (deve corrispondere allo slug del menu principale per questa sottovoce)
        'manual_generation_admin_page' // Funzione per il contenuto della pagina
    );
}
add_action('admin_menu', 'setup_admin_menu');
