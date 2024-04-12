<?php

function register_automatic_generation_settings() {
    register_setting('paginedacolorare_ai_automatic_generation_options_group', 'auto_generation_interval_secs');
    register_setting('paginedacolorare_ai_automatic_generation_options_group', 'coloring_page_topics');
}

function automatic_generation_settings_page() {
    ?>
    <div>
        <h2>Automatic Content Generation Settings</h2>

        <?php
        $next_scheduled = wp_next_scheduled( 'paginedacolorare_ai_cron_hook' );
        echo "<h3>Next article generation scheduled on:</h3>";
        echo(date('Y-m-d H:i:s', $next_scheduled) . " - " . date_default_timezone_get());

        echo "<h3>Random Topic:</h3>";
        echo "<p>" . getRandomTopic() . "</p>";
        ?>

        <div class="wrap">
            <h2>Automatic Generation Settings</h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('paginedacolorare_ai_automatic_generation_options_group');
                do_settings_sections('paginedacolorare_ai_automatic_generation_options_group');
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Auto-Generate a new article every (seconds):</th>
                        <td>
                            <p><em><strong>Please Note:</strong> If you change the interval, you must deactivate and reactivate the plugin to let the change take effect.</em></p>
                            <br/>
                            <input type="text" name="auto_generation_interval_secs" value="<?php echo esc_attr(get_option('auto_generation_interval_secs', 3963)); ?>" />
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Topics:</th>
                        <td>
                            <p><em>Type one topic per line, example topic: Fantasy landscape with dragons. At each generation, one of the following topic is randomly choosen to generate an article.</em></p>
                            </br>
                            <textarea cols=80 rows=10 name="coloring_page_topics"><?php echo get_option('coloring_page_topics'); ?></textarea>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
    </div>
    <?php
}
