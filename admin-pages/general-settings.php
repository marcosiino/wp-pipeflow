<?php

function register_general_settings() {
    register_setting('paginedacolorare_ai_general_options_group', 'openai_api_key');
}
/**
 * General Settings
 */
function general_plugin_settings()
{
    ?>
    <div class="wrap">
        <h2>OpenAI Settings</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('paginedacolorare_ai_general_options_group');
            do_settings_sections('paginedacolorare_ai_general_options_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">OpenAI API Key</th>
                    <td><input type="text" name="openai_api_key" value="<?php echo esc_attr(get_option('openai_api_key')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}