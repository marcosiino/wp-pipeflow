<?php
interface AICompletionServiceInterface {
    public function perform_text_completion(string $prompt, string $image_attachment_url = null);
    public function perform_image_completion(string $prompt);
}

?>