<?php
define('DEFAULT_AUTO_GENERATION_INTERVAL', 43512);

const PIPELINE_CONFIGURATION_JSON_DEFAULT = "
{
    \"stages\": []
}
";

class Settings {
    public static function get_auto_generation_interval_secs() {
        return esc_attr(get_option('auto_generation_interval_secs', DEFAULT_AUTO_GENERATION_INTERVAL));
    }

    /**
     * @return int The Content Generation Pipeline configuration JSON
     */
    public static function get_pipeline_configuration_json() {
        return get_option('pipeline_configuration_json', PIPELINE_CONFIGURATION_JSON_DEFAULT);
    }
}