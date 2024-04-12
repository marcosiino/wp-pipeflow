<?php
require_once(PLUGIN_PATH . 'ai-generation-utils/image-generation.php');
require_once(PLUGIN_PATH . 'ai-generation-utils/text-generation.php');
require_once(PLUGIN_PATH . 'utils/utils.php');
require_once(PLUGIN_PATH . 'utils/defaults.php');

function getRandomTopic() {
    $topics_str = get_option('coloring_page_topics');
    if(isset($topics_str) AND empty($topics_str) == false) {
        $topics_array = explode("\n", $topics_str);
        $random_index = random_int(0,count($topics_array)-1);
        return $topics_array[$random_index];
    }
    else {
        return "";
    }
}

function generateRandomTopicArticle() {
    $topic = getRandomTopic();
    generateNewArticle($topic);
}
function generateNewArticle($topic) {
    $input_params = array(
        array(
            "key" => "TOPIC",
            "value" => $topic,
        )
    );

    $result = generate_with_mode(get_option('image_first_flow', IMAGE_FIRST_DEFAULT), $input_params);
    $generatedImageData = $result['generatedImage'];
    $generatedTextData = $result['generatedText'];

    if (isset($generatedImageData) && isset($generatedTextData)) {
        echo "<p style='color: blue;'>Inserting the new post...</p>";
        insert_post($generatedTextData['title'], $generatedTextData['description'], $generatedTextData['category_ids'], $generatedTextData['tag_ids'], $generatedImageData['image_id'], 'publish');
        echo "<p><strong>Post generated successful!</strong></p>";
    }
    else {
        echo "<p style='color: red;'><strong>Article generation failed.</strong></p>";
    }
}

function generate_with_mode($image_first_mode, $input_params) {
    if($image_first_mode) {
        echo "<p style='color: blue;'>Generating the image (image first mode = true)...</p>";
        $generatedImageData = generate_image($input_params);
        echo "<p style='color: blue;'>Generating the text description for the image...</p>";
        $generatedTextData = generate_text(array(), $generatedImageData['image_url']);

        return array(
            "generatedText" => $generatedTextData,
            "generatedImage" => $generatedImageData,
        );
    }
    else {
        echo "<p style='color: blue;'>Generating the text description (image first mode = false)...</p>";
        $generatedTextData = generate_text($input_params, null);

        echo "<p style='color: blue;'>Generating the image for the description</p>";

        $image_generation_inputs = array(
            array(
                "key" => "GENERATED_ARTICLE_DESCRIPTION",
                "value" => $generatedTextData['description'],
            ),
            array(
                "key" => "GENERATED_ARTICLE_TITLE",
                "value" => $generatedTextData['title'],
            ),
        );

        $generatedImageData = generate_image($image_generation_inputs);


        return array(
            "generatedText" => $generatedTextData,
            "generatedImage" => $generatedImageData,
        );
    }
}