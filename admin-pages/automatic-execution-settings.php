<?php
require_once(ABSPATH . "wp-content/plugins/wp-pipeflow/utils/settings.php");

function register_automatic_execution_settings() {
    register_setting('pipeflow_automatic_generation_options_group', 'auto_generation_interval_secs');
}

function automatic_execution_settings_page() {
    ?>
    <div>
        <h2>Automatic pipeline execution settings</h2>

        <?php
        $next_scheduled = wp_next_scheduled( 'pipeflow_cron_hook' );
        echo "<h3>Next execution is scheduled on:</h3>";
        echo(date('Y-m-d H:i:s', $next_scheduled) . " - " . date_default_timezone_get());
        ?>

        <div class="wrap">
            <h2>Auto execution Settings</h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('pipeflow_automatic_generation_options_group');
                do_settings_sections('pipeflow_automatic_generation_options_group');
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Auto execute the pipeline every (seconds):</th>
                        <td>
                            <p><em><strong>Please Note:</strong> If you change the interval, you must deactivate and reactivate the plugin to let the change take effect immediately.</em></p>
                            <br/>
                            <input type="text" name="auto_generation_interval_secs" value="<?php echo Settings::get_auto_generation_interval_secs(); ?>" />
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
    </div>
    <?php
}
