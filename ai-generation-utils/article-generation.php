<?php
require_once(PLUGIN_PATH . 'ai-generation-utils/image-generation.php');
require_once(PLUGIN_PATH . 'ai-generation-utils/text-generation.php');
require_once(PLUGIN_PATH . 'utils/utils.php');

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
    echo "<p style='color: blue;'>Generating an image about: $topic...</p>";
    $generatedImageData = generateImage($topic);
    echo "<p style='color: blue;'>Generating the text description for the image...</p>";
    $generatedData = generateText($generatedImageData['image_url']);

    if (isset($generatedImageData) && isset($generatedData)) {
        echo "<p style='color: blue;'>Inserting the new post...</p>";
        insert_post($generatedData['title'], $generatedData['description'], $generatedData['category_ids'], $generatedData['tag_ids'], $generatedImageData['image_id'], 'publish');
        echo "<p><strong>Post generated successful!</strong></p>";
    }
    else {
        echo "<p style='color: red;'><strong>Article generation failed.</strong></p>";
    }
}
