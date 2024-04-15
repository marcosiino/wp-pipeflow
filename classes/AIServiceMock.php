<?php
require_once(PLUGIN_PATH . "classes/AICompletionServiceInterface.php");

class AIServiceMock implements AITextCompletionServiceInterface, AIImageCompletionServiceInterface {

    public function perform_text_completion(string $prompt, string $image_attachment_url = null)
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
}