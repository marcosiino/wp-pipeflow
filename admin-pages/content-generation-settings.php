<?php
require_once(PLUGIN_PATH . "utils/defaults.php");

function register_content_generation_settings() {
    register_setting('paginedacolorare_ai_content_options_group', 'image_generation_prompt');
    register_setting('paginedacolorare_ai_content_options_group', 'article_generation_prompt');
    register_setting('paginedacolorare_ai_content_options_group', 'image_first_flow');
    register_setting('paginedacolorare_ai_content_options_group', 'automatic_categories_and_tags');
}
function print_placeholders() {
    $imageFirst = get_option('image_first_flow', IMAGE_FIRST_DEFAULT);
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
        <td><textarea cols=80 rows=10 name="image_generation_prompt"><?php echo esc_attr(get_option('image_generation_prompt', IMAGE_DEFAULT_PROMPT)); ?></textarea></td>
    </tr>
    <?php
}

function print_text_prompt_field() {
    ?>
    <tr valign="top">
        <th scope="row">Coloring Page Article Title and Text Generation Prompt</th>
        <td><textarea cols=80 rows=10 name="article_generation_prompt"><?php echo esc_attr(get_option('article_generation_prompt', TEXT_DEFAULT_PROMPT)); ?></textarea></td>
    </tr>
    <?php
}

function content_generation_settings_page() {
    $image_first = get_option('image_first_flow', IMAGE_FIRST_DEFAULT)
    ?>
    <div class="wrap">
        <h2>Content Generation Settings</h2>
        <?php print_placeholders(); ?>

        <form method="post" action="options.php">
            <?php
            settings_fields('paginedacolorare_ai_content_options_group');
            do_settings_sections('paginedacolorare_ai_content_options_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Image First Mode*</th>
                    <td>
                        <input type="checkbox" name="image_first_flow" value="1" <?php checked("1", esc_attr(get_option("image_first_flow"), IMAGE_FIRST_DEFAULT)); ?> />
                        <em>* (Image First mode means that an image is generated firstly, then a description is generated based on that image. If this mode is disabled, firstly a description is generated about the topic, then an image is generated based on the description generated in the first step).</em>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Automatic Assign Categories and Tags *</th>
                    <td>
                        <input type="checkbox" name="automatic_categories_and_tags" value="1" <?php checked("1", esc_attr(get_option("automatic_categories_and_tags"), AUTOMATIC_CATEGORIES_AND_TAGS_DEFAULT)); ?> />
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