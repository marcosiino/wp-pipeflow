<?php
require_once(PLUGIN_PATH . 'ai-generation-utils/image-generation.php');
require_once(PLUGIN_PATH . 'ai-generation-utils/text-generation.php');
require_once(PLUGIN_PATH . 'utils/utils.php');

function generateNewArticle($topic) {
    $generatedImageData = generateImage($topic);
    $generatedData = generateText($generatedImageData['image_url']);

    if (isset($generatedImageData) && isset($generatedData)) {
        insert_post($generatedData['title'], $generatedData['description'], $generatedData['category_ids'], $generatedData['tag_ids'], $generatedImageData['image_id'], 'publish');
        echo "<p><strong>Post generated successful!</strong></p>";
    }
    else {
        echo "<p><strong>Article generation failed.</strong></p>";
    }
}
