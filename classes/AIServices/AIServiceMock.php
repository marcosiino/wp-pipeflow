<?php

namespace AIServices;
require_once PLUGIN_PATH . "classes/AIServices/AICompletionServiceInterface.php";

class AIServiceMock implements AITextCompletionServiceInterface, AIImageCompletionServiceInterface
{

    public function perform_text_completion(string $prompt, bool $return_json_response, array $image_attachment_urls = null, float $temperature = 0.7, int $max_tokens = 4096)
    {
        return json_encode(array(
            "title" => "A mock article",
            "description" => "An example description",
        ));
    }

    public function perform_image_completion(string $prompt)
    {
        return "https://fastly.picsum.photos/id/519/200/200.jpg?hmac=7MwcBjyXrRX5GB6GuDATVm_6MFDRmZaSK7r5-jqDNS0";
    }

    public function perform_categories_and_tags_assignment_completion(string $content, array $available_categories, array $available_tags, $max_categories_num, $max_tags_num)
    {
        return array(
            "categories_ids" => array(1),
            "tags_ids" => array(1, 2),
        );
    }
}