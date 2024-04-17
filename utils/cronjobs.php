<?php
require_once(PLUGIN_PATH . "classes/ArticleGenerator.php");
require_once(PLUGIN_PATH . "utils/settings.php");

/**
 * Launch the job to execute when cron job is triggered
 */
function cron_exec() {
    if(ArticleGenerator::check_settings() === true) {
        $articleGenerator = new ArticleGenerator();
        $articleGenerator->generate_new_article(); //generate a random topic article
    }
}

function cron_interval( $schedules ) {
    $schedules['generate_content_interval'] = array(
        'interval' => Settings::get_auto_generation_interval_secs(),
        'display'  => esc_html__( 'Every ' . Settings::get_auto_generation_interval_secs() . ' Seconds' ), );
    return $schedules;
}

function schedule_cronjobs() {
    if ( ! wp_next_scheduled( 'postbrewer_cron_hook' ) ) {
        wp_schedule_event( time(), 'generate_content_interval', 'postbrewer_cron_hook' );
    }
}

function unschedule_cronjobs() {
    $timestamp = wp_next_scheduled( 'postbrewer_cron_hook' );
    wp_unschedule_event( $timestamp, 'postbrewer_cron_hook' );
}

function setup_cronjobs() {
    // WP Cron
    add_action( 'postbrewer_cron_hook', 'cron_exec' ); //Custom hook for cron job execution (cron_exec function is defined in cronjobs.php)
    add_filter( 'cron_schedules', 'cron_interval' ); // Defines the cron time interval
}