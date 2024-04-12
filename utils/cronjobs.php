<?php
/**
 * Launch the job to execute when cron job is triggered
 */
function cron_exec() {
    generateRandomTopicArticle();
}

function cron_interval( $schedules ) {
    $schedules['generate_content_interval'] = array(
        'interval' => get_option('auto_generation_interval_secs', DEFAULT_AUTO_GENERATION_INTERVAL),
        'display'  => esc_html__( 'Every ' . get_option('auto_generation_interval_secs', DEFAULT_AUTO_GENERATION_INTERVAL) . ' Seconds' ), );
    return $schedules;
}

function schedule_cronjobs() {
    if ( ! wp_next_scheduled( 'paginedacolorare_ai_cron_hook' ) ) {
        wp_schedule_event( time(), 'generate_content_interval', 'paginedacolorare_ai_cron_hook' );
    }
}

function unschedule_cronjobs() {
    $timestamp = wp_next_scheduled( 'paginedacolorare_ai_cron_hook' );
    wp_unschedule_event( $timestamp, 'paginedacolorare_ai_cron_hook' );
}

function setup_cronjobs() {
    // WP Cron
    add_action( 'paginedacolorare_ai_cron_hook', 'cron_exec' ); //Custom hook for cron job execution (cron_exec function is defined in cronjobs.php)
    add_filter( 'cron_schedules', 'cron_interval' ); // Defines the cron time interval
}