<?php
/**
 * Plugin Name: PagineDaColorare.it AI Bot
 * Plugin URI: https://paginedacolorare.it
 * Description: Questo plugin genera disegni da colorare per il sito utilizzando l'intelligenza artificiale.
 * Version: 1.0
 * Author: Marco Siino
 * Author URI: http://marcosiino.it
 */

require_once('utils.php');
require_once('image-generation.php');

// Prevenire l'accesso diretto al file del plugin.
defined('ABSPATH') or die('Accesso non permesso.');

// Setting a custom timeout value for cURL. Using a high value for priority to ensure the function runs after any other added to the same action hook.
add_action('http_api_curl', 'sar_custom_curl_timeout', 9999, 1);
function sar_custom_curl_timeout( $handle ){
    curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 30 ); // 30 seconds.
    curl_setopt( $handle, CURLOPT_TIMEOUT, 30 ); // 30 seconds.
}

// Setting custom timeout for the HTTP request
add_filter( 'http_request_timeout', 'sar_custom_http_request_timeout', 9999 );
function sar_custom_http_request_timeout( $timeout_value ) {
    return 30; // 30 seconds.
}

// Setting custom timeout in HTTP request args
add_filter('http_request_args', 'sar_custom_http_request_args', 9999, 1);
function sar_custom_http_request_args( $r ){
    $r['timeout'] = 30; // 30 seconds.
    return $r;
}

/**
 * Attivazione del Plugin.
 */
function paginedacolorare_ai_attivazione() {
    // Azioni da eseguire al momento dell'attivazione del plugin.
}
register_activation_hook(__FILE__, 'paginedacolorare_ai_attivazione');

/**
 * Disattivazione del Plugin.
 */
function paginedacolorare_ai_disattivazione() {
    // Azioni da eseguire al momento della disattivazione del plugin.
}
register_deactivation_hook(__FILE__, 'paginedacolorare_ai_disattivazione');

/**
 * Inizializzazione del Plugin.
 */
function paginedacolorare_ai_init() {
    // Qui il tuo codice per inizializzare il plugin.
}
add_action('init', 'paginedacolorare_ai_init');


/**
 * Register the plugin settings
 */
function paginedacolorare_ai_register_settings() {
    register_setting('paginedacolorare_ai_options_group', 'paginedacolorare_ai_openai_api_key');
}
add_action('admin_init', 'paginedacolorare_ai_register_settings');

/**
 * Aggiunge il menu di impostazioni del plugin.
 */
function paginedacolorare_ai_add_admin_menu() {
    add_menu_page(
        'Settings', // Titolo della pagina
        'AI Bot', // Titolo del menu
        'manage_options', // Capability
        'paginedacolorare-ai', // Slug del menu
        'paginedacolorare_ai_settings_page', // Funzione per visualizzare la pagina di impostazioni
        'dashicons-admin-customizer', // Icona del menu
        6 // Posizione nel menu
    );

    // Aggiunge la stessa pagina del menu principale come sottovoce
    add_submenu_page(
        'paginedacolorare-ai', // Slug del menu principale
        'Dashboard', // Titolo della pagina
        'Dashboard', // Titolo del menu
        'manage_options', // Capability
        'paginedacolorare-ai-dashboard', // Slug della pagina (deve corrispondere allo slug del menu principale per questa sottovoce)
        'paginedacolorare_ai_content_dashboard_page' // Funzione per il contenuto della pagina
    );
}
add_action('admin_menu', 'paginedacolorare_ai_add_admin_menu');


/**
 * Funzione per visualizzare la pagina delle impostazioni del plugin
 */
function paginedacolorare_ai_settings_page()
{
    ?>
    <div class="wrap">
        <h2>OpenAI Settings</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('paginedacolorare_ai_options_group');
            do_settings_sections('paginedacolorare_ai_options_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">OpenAI API Key</th>
                    <td><input type="text" name="paginedacolorare_ai_openai_api_key" value="<?php echo esc_attr(get_option('paginedacolorare_ai_openai_api_key')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Funzione per visualizzare la pagina di generazione dei contenuti
 */
function paginedacolorare_ai_content_dashboard_page() {
    echo '<div class="wrap"><h2>PagineDaColorare.it AI Bot</h2>';


    // Se il pulsante di generazione è stato premuto
    if (isset($_POST['action']) && $_POST['action'] == 'generate') {
        $topic = $_POST['topic'];
        echo "<pre>";
        print_r(generateImage($topic));
        echo "</pre>";

        echo '<textarea rows="10" cols="50">TODO: generare il disegno e la descrizione, mostrarli qui e poi salvare l\'articolo come bozza</textarea>';
    }

    // Pulsante di generazione
    echo '<form method="post">';
    echo '<input name="topic">';
    echo '<input type="hidden" name="action" value="generate">';
    submit_button('Genera Disegno');
    echo '</form>';

    echo '</div>';
}