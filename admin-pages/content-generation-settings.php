<?php
require_once(PLUGIN_PATH . "utils/defaults.php");

function register_content_generation_settings() {
    register_setting('paginedacolorare_ai_content_options_group', 'image_generation_prompt');
    register_setting('paginedacolorare_ai_content_options_group', 'article_generation_prompt');
}

function content_generation_settings_page() {
    ?>global$image_default_prompt;
    <div class="wrap">
        <h2>Content Generation Settings</h2>

        <div style="border: 1px solid black; margin: 2em 0;">
            <h3>Placeholders for Image Generation Prompt:</h3>
            <ul>
                <li>Coloring Page Topic: <strong>%TOPIC%</strong></li>
            </ul>
        </div>

        <div style="border: 1px solid black; margin: 2em 0;">
            <h3>Placeholders for Text Generation Prompt:</h3>
            <ul>
                <li>Available categories in wordpress (id and names): <strong>%CATEGORIES%</strong></li>
                <li>Available tags in wordpress (id and names): <strong>%TAGS%</strong></li>
            </ul>

            <strong>Note: </strong> the image url of the generated coloring page image is added to the prompt automatically to be analyzed by vision. You should mention in the text prompt to create a description for the coloring page in the image attached.
        </div>

        <form method="post" action="options.php">
            <?php
            settings_fields('paginedacolorare_ai_content_options_group');
            do_settings_sections('paginedacolorare_ai_content_options_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Coloring Page Image Generation Prompt</th>
                    <td><textarea cols=80 rows=10 name="image_generation_prompt"><?php echo esc_attr(get_option('image_generation_prompt', IMAGE_DEFAULT_PROMPT)); ?></textarea></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Coloring Page Article Title and Text Generation Prompt</th>
                    <td><textarea cols=80 rows=10 name="article_generation_prompt"><?php echo esc_attr(get_option('article_generation_prompt', TEXT_DEFAULT_PROMPT)); ?></textarea></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
