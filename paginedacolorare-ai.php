<?php
/**
 * Plugin Name: PagineDaColorare.it AI
 * Plugin URI: https://paginedacolorare.it
 * Description: Questo plugin genera pagine da colorare utilizzando l'intelligenza artificiale.
 * Version: 1.0
 * Author: Marco Siino
 * Author URI: http://marcosiino.it
 */

// Prevenire l'accesso diretto al file del plugin.
defined('ABSPATH') or die('Accesso non permesso.');

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
