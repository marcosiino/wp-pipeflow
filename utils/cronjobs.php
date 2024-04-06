<?php
/**
 * Launch the job to execute when cron job is triggered
 */
function cron_exec() {
    generateNewArticle("");
}

function cron_interval( $schedules ) {
    $schedules['generate_content_interval'] = array(
        'interval' => 3963,
        'display'  => esc_html__( 'Every 3963 Seconds' ), );
    return $schedules;
}

function schedule_cronjobs() {
    if ( ! wp_next_scheduled( 'paginedacolorare_ai_cron_hook' ) ) {
        wp_schedule_event( time(), 'generate_content_interval', 'paginedacolorare_ai_cron_hook' );
    }
}

function unschedule_cronjobs() {
    $timestamp = wp_next_scheduled( 'pdc_ai_cron_hook' );
    wp_unschedule_event( $timestamp, 'pdc_ai_cron_hook' );
}

function setup_cronjobs() {
    // WP Cron
    add_action( 'paginedacolorare_ai_cron_hook', 'cron_exec' ); //Custom hook for cron job execution (cron_exec function is defined in cronjobs.php)
    add_filter( 'cron_schedules', 'cron_interval' ); // Defines the cron time interval
}