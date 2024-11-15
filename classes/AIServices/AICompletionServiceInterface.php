<?php
namespace AIServices;

interface AITextCompletionServiceInterface {
    public function perform_text_completion(string $prompt, bool $return_json_response, array $image_attachment_urls = null, float $temperature, int $max_tokens);
}

interface AIImageCompletionServiceInterface {
    public function perform_image_completion(string $prompt);
}

interface AICategoriesAndTagsCompletionServiceInterface {
    public function perform_categories_and_tags_assignment_completion(string $content, array $available_categories, array $available_tags, $max_categories_num, $max_tags_num);
}
interface AICompletionServiceInterface extends AITextCompletionServiceInterface, AIImageCompletionServiceInterface, AICategoriesAndTagsCompletionServiceInterface { }
?>