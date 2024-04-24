<?php
require_once(PLUGIN_PATH . "utils/settings.php");

function register_pipeline_configuration_setup_settings() {
    register_setting('postbrewer_pipeline_setup_group', 'pipeline_configuration_json');
}
function pipeline_configuration_page() {
    ?>
    <div class="wrap">
        <h2>Content Generation Pipeline Setup</h2>

        <form method="post" action="options.php">
            <?php
            settings_fields('postbrewer_pipeline_setup_group');
            do_settings_sections('postbrewer_pipeline_setup_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Pipeline Configuration JSON</th>
                    <td><textarea cols=80 rows=10 name="pipeline_configuration_json"><?php echo Settings::get_pipeline_configuration_json(); ?></textarea></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}