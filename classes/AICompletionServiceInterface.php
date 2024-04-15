<?php
interface AITextCompletionServiceInterface {
    public function perform_text_completion(string $prompt, string $image_attachment_url = null);
}

interface AIImageCompletionServiceInterface {
    public function perform_image_completion(string $prompt);
}

interface AICompletionServiceInterface extends AITextCompletionServiceInterface, AIImageCompletionServiceInterface { }
?>