<?php
/**
 * Plugin Name: WP-PipeFlow
 * Plugin URI: https://marcosiino.it
 * Description: Create your pipelines to automatize wordpress tasks by concatenating the available core stages, third-party stages or your own custom stages.
 * Version: 1.2.0
 * Author: Marco Siino
 * Author URI: http://marcosiino.it
 * License: GPL v3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

defined('ABSPATH') or die('Accesso non permesso.');

define('WP_PIPEFLOW_PLUGIN_PATH', plugin_dir_path(__FILE__));

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StagesRegistration.php";

require_once(ABSPATH . 'wp-content/plugins/wp-pipeflow/utils/utils.php');
require_once(ABSPATH . 'wp-content/plugins/wp-pipeflow/utils/cronjobs.php');
require_once(ABSPATH . 'wp-content/plugins/wp-pipeflow/utils/http_requests_timeout_settings.php');

require_once(ABSPATH . 'wp-content/plugins/wp-pipeflow/admin-pages/pipeline-configuration.php');
require_once(ABSPATH . 'wp-content/plugins/wp-pipeflow/admin-pages/automatic-execution-settings.php');
require_once(ABSPATH . 'wp-content/plugins/wp-pipeflow/admin-pages/manual-execution.php');

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
    register_pipeline_configuration_setup_settings();
    register_automatic_execution_settings();
}
add_action('admin_init', 'register_plugin_settings');

function pipeflow_enqueue_admin_css($hook) {
    if (!str_contains($hook, "wp-pipeflow")) {
        return;
    }

    wp_enqueue_style(
        'pipeflow-admin-css', // Handle unico
        plugin_dir_url(__FILE__) . 'css/pipeflow-admin.css',
        [], // Dependencies
        '1.0', // Version
        'all' // Media (all, screen, print, ecc.)
    );
}
add_action('admin_enqueue_scripts', 'pipeflow_enqueue_admin_css');

/**
 * Enqueue CodeMirror code editor
 */
function pipeflow_enqueue_codemirror($hook) {
    //Enqueue only if the hook is one of the plugin's admin pages specified above (to avoid loading the codemirror script in other places)
    if (!str_contains($hook, "wp-pipeflow")) {
        return;
    }

    // Check if CodeMirror is available WordPress
    if (wp_script_is('code-editor', 'registered')) {
        wp_enqueue_script('code-editor');
        wp_enqueue_style('code-editor');
    } else {
        // Fallback: Carica CodeMirror from a CDN
        wp_enqueue_style('codemirror-css', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css');
        wp_enqueue_script('codemirror-js', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.js', [], null, true);
        wp_enqueue_script('codemirror-mode-xml', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/xml/xml.min.js', ['codemirror-js'], null, true);
    }

    // Inizializzazione personalizzata
    wp_add_inline_script(
        'code-editor',
        'jQuery(function($) {
        var editorSettings = {
            lineNumbers: true,
            mode: "xml",
            indentUnit: 14,
            matchBrackets: true,
            autoCloseTags: true,
            lineWrapping: true,
        };

        var editor = wp.codeEditor.initialize($("#pipeflow-configuration-editor"), editorSettings);
        console.log("Editor settings:", editorSettings);
        console.log("CodeMirror instance:", editor.codemirror);
        });'
    );
}
add_action('admin_enqueue_scripts', 'pipeflow_enqueue_codemirror');

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
    // Main Menu /
    add_menu_page(
        'Pipeline Configuration', //Page Title
        'WP-PipeFlow', // Menu title
        'manage_options', // Capability
        'wp-pipeflow', // Menu slug
        'pipeline_configuration_page', // Admin functions
        'dashicons-admin-customizer', // Menu Icon
        6 // Position
    );

    // Automatic Generation Settings Menu Item
    add_submenu_page(
        'wp-pipeflow', // Slug del menu principale
        'Auto Execution Settings', // Titolo della pagina
        'Auto Execution Settings', // Titolo del menu
        'manage_options', // Capability
        'wp-pipeflow-auto-generation-settings', // Slug della pagina (deve corrispondere allo slug del menu principale per questa sottovoce)
        'automatic_execution_settings_page' // Funzione per il contenuto della pagina
    );

    // Manual Content Generation Menu Item
    add_submenu_page(
        'wp-pipeflow', // Slug del menu principale
        'Manual pipeline execution', // Titolo della pagina
        'Manual Execution', // Titolo del menu
        'manage_options', // Capability
        'wp-pipeflow-manual-execution', // Slug della pagina (deve corrispondere allo slug del menu principale per questa sottovoce)
        'manual_execution_admin_page' // Funzione per il contenuto della pagina
    );
}
add_action('admin_menu', 'setup_admin_menu');
