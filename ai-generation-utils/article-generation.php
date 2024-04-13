<?php
require_once(PLUGIN_PATH . 'classes/ArticleGenerator.php');
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
    $generatedArticle = $result['generatedArticle'];

    if (isset($generatedImageData) && isset($generatedArticle)) {
        echo "<p style='color: blue;'>Inserting the new post...</p>";
        $category_ids = array(); //TODO
        $tag_ids = array();
        insert_post($generatedArticle->title, $generatedArticle->description, $category_ids, $tag_ids, $generatedImageData->savedImageId, 'publish');
        echo "<p><strong>Post generated successful!</strong></p>";
    }
    else {
        echo "<p style='color: red;'><strong>Article generation failed.</strong></p>";
    }
}

function generate_with_mode($image_first_mode, $input_params) {
    $articleGenerator = new ArticleGenerator();
    if($image_first_mode) {
        echo "<p style='color: blue;'>Generating the image (image first mode = true)...</p>";
        $generatedImageData = $articleGenerator->generate_and_save_image($input_params);
        echo "<p style='color: blue;'>Generating the text description for the image...</p>";
        $generatedArticle = $articleGenerator->generate_article(array(), $generatedImageData->generatedImageExternalURL);

        return array(
            "generatedArticle" => $generatedArticle,
            "generatedImage" => $generatedImageData,
        );
    }
    else {
        echo "<p style='color: blue;'>Generating the text description (image first mode = false)...</p>";
        $generatedArticle = $articleGenerator->generate_article($input_params, null);

        echo "<p style='color: blue;'>Generating the image for the description</p>";

        $image_generation_inputs = array(
            array(
                "key" => "GENERATED_ARTICLE_DESCRIPTION",
                "value" => $generatedArticle->description,
            ),
            array(
                "key" => "GENERATED_ARTICLE_TITLE",
                "value" => $generatedArticle->title,
            ),
        );

        $generatedImageData = $articleGenerator->generate_and_save_image($image_generation_inputs);

        return array(
            "generatedArticle" => $generatedArticle,
            "generatedImage" => $generatedImageData,
        );
    }
}