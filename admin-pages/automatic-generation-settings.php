<?php
require_once(WP_PIPEFLOW_PLUGIN_PATH . "utils/settings.php");

function register_automatic_generation_settings() {
    register_setting('postbrewer_automatic_generation_options_group', 'auto_generation_interval_secs');
    register_setting('postbrewer_automatic_generation_options_group', 'postbrewer_content_topics');
}

function automatic_generation_settings_page() {
    ?>
    <div>
        <h2>Automatic Content Generation Settings</h2>

        <?php
        $next_scheduled = wp_next_scheduled( 'postbrewer_cron_hook' );
        echo "<h3>Next article generation scheduled on:</h3>";
        echo(date('Y-m-d H:i:s', $next_scheduled) . " - " . date_default_timezone_get());
        ?>

        <div class="wrap">
            <h2>Automatic Generation Settings</h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('postbrewer_automatic_generation_options_group');
                do_settings_sections('postbrewer_automatic_generation_options_group');
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Auto-Generate a new article every (seconds):</th>
                        <td>
                            <p><em><strong>Please Note:</strong> If you change the interval, you must deactivate and reactivate the plugin to let the change take effect.</em></p>
                            <br/>
                            <input type="text" name="auto_generation_interval_secs" value="<?php echo Settings::get_auto_generation_interval_secs(); ?>" />
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Topics:</th>
                        <td>
                            <p><em>Type one topic per line, example topic: Fantasy landscape with dragons. At each generation, one of the following topic is randomly choosen to generate an article.</em></p>
                            </br>
                            <textarea cols=80 rows=10 name="postbrewer_content_topics"><?php echo Settings::get_content_generation_topics(); ?></textarea>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
    </div>
    <?php
}
