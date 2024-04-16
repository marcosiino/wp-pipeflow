<?php
require_once(PLUGIN_PATH . "utils/settings.php");

function register_content_generation_settings() {
    register_setting('postbrewer_content_options_group', 'image_generation_prompt');
    register_setting('postbrewer_content_options_group', 'article_generation_prompt');
    register_setting('postbrewer_content_options_group', 'image_first_flow');
    register_setting('postbrewer_content_options_group', 'automatic_categories_and_tags');
    register_setting('postbrewer_content_options_group', 'image_generation_openai_model');
    register_setting('postbrewer_content_options_group', 'image_generation_enable_hd');
    register_setting('postbrewer_content_options_group', 'image_generation_size');
    register_setting('postbrewer_content_options_group', 'text_generation_openai_model');
    register_setting('postbrewer_content_options_group', 'text_generation_temperature');
}
function print_placeholders() {
    $imageFirst = Settings::get_image_first_mode();
    if($imageFirst == true) {
        readfile(PLUGIN_PATH . "admin-pages/html/image_first_placeholders.inc.html");
    } else {
        readfile(PLUGIN_PATH . "admin-pages/html/text_first_placeholders.inc.html");
    }
}

function print_image_prompt_field() {
    ?>
    <tr valign="top">
        <th scope="row">Image Generation Prompt</th>
        <td><textarea cols=80 rows=10 name="image_generation_prompt"><?php echo Settings::get_image_generation_prompt(); ?></textarea></td>
    </tr>

    <tr valign="top">
        <th scope="row">Image Generation OpenAI Model</th>
        <td>
            <input type="text" name="image_generation_openai_model" value="<?php echo Settings::get_image_generation_openai_model() ?>" />
        </td>
    </tr>

    <tr valign="top">
        <th scope="row">Use HD Quality for Image Generation*</th>
        <td>
            <input type="checkbox" name="image_generation_enable_hd" value="1" <?php checked("1", Settings::get_image_generation_enable_hd()); ?> />
            <em>* If enabled, the generated image is more consistent, but are more expensive.</em>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row">Image Generation Size</th>
        <td>
            <input type="text" name="image_generation_size" value="<?php echo Settings::get_image_generation_size() ?>" />
        </td>
    </tr>
    <?php
}

function print_text_prompt_field() {
    ?>
    <tr valign="top">
        <th scope="row">Content Generation AI Prompts</th>
        <td><textarea cols=80 rows=10 name="article_generation_prompt"><?php echo Settings::get_article_generation_prompt(); ?></textarea></td>
    </tr>

    <tr valign="top">
        <th scope="row">Text Generation OpenAI Model *</th>
        <td>
            <input type="text" name="text_generation_openai_model" value="<?php echo Settings::get_text_generation_openai_model() ?>" />
        </td>
    </tr>

    <tr valign="top">
        <th scope="row">Text Generation Temperature</th>
        <td>
            <input type="text" name="text_generation_temperature" value="<?php echo Settings::get_text_generation_temperature() ?>" />
        </td>
    </tr>
    <?php
}

function content_generation_settings_page() {
    $image_first = Settings::get_image_first_mode();
    ?>
    <div class="wrap">
        <h2>Content Generation Settings</h2>
        <?php print_placeholders(); ?>

        <form method="post" action="options.php">
            <?php
            settings_fields('postbrewer_content_options_group');
            do_settings_sections('postbrewer_content_options_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Image First Mode*</th>
                    <td>
                        <input type="checkbox" name="image_first_flow" value="1" <?php checked("1", Settings::get_image_first_mode()); ?> />
                        <em>* (Image First mode means that an image is generated firstly, then a description is generated based on that image. If this mode is disabled, firstly a description is generated about the topic, then an image is generated based on the description generated in the first step).</em>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Automatic Assign Categories and Tags *</th>
                    <td>
                        <input type="checkbox" name="automatic_categories_and_tags" value="1" <?php checked("1", Settings::get_automatic_categories_and_tags()); ?> />
                        <em>* If selected, a separated AI completion is performed to ask the AI to choose which category and tags to associate to the generated article, by choosing from the categories and tags already available in this website.</em>
                    </td>
                </tr>

                <?php
                    if($image_first == true) {
                        print_image_prompt_field();
                        print_text_prompt_field();
                    }
                    else {
                        print_text_prompt_field();
                        print_image_prompt_field();
                    }
                ?>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
