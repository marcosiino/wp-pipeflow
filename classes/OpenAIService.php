<?php
require_once(PLUGIN_PATH . "classes/AICompletionErrors.php");
require_once(PLUGIN_PATH . "classes/AICompletionServiceInterface.php");

/**
 * OpenAI Client
 */
class OpenAIService implements AITextCompletionServiceInterface, AIImageCompletionServiceInterface
{
    private $apiKey;
    private $textCompletionsModel;
    private $imageCompletionsModel;
    private $imageCompletionSize;
    private $imageCompletionHDQuality;

    public function __construct(string $apiKey, string $textCompletionsModel = "gpt-4-turbo", string $imageCompletionsModel = "dall-e-3", string $imageCompletionSize = "1024x1024", bool $imageCompletionHDQuality = false)
    {
        $this->apiKey = $apiKey;
        $this->textCompletionsModel = $textCompletionsModel;
        $this->imageCompletionsModel = $imageCompletionsModel;
        $this->imageCompletionSize = $imageCompletionSize;
        $this->imageCompletionHDQuality = $imageCompletionHDQuality;
    }

    /**
     * Performs a text generation request to the AI, with an optional image attachment url
     * @throws AICompletionException
     */
    public function perform_text_completion(string $prompt, string $image_attachment_url = null, float $temperature = 0.7, int $max_tokens = 4096)
    {
        if (!isset($this->apiKey)) {
            throw new AICompletionException("Api key not set");
        }

        $content = array();
        $content[] = array(
            "type" => "text",
            "text" => $prompt,
        );

        // Adds the image url to the content and switch to "vision"
        if(isset($image_attachment_url)) {
            $content[] = array(
                "type" => "image_url",
                "image_url" => array(
                    "url" => $image_attachment_url,
                )
            );
        }

        $body = array(
            "model" => $this->textCompletionsModel,
            "messages" => [
                array(
                    "role" => "user",
                    "content" => $content,
                )
            ],
            "temperature" => $temperature,
            "max_tokens" => $max_tokens,
        );

        $args = array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ),
            'method' => 'POST',
            'body' => json_encode($body)
        );

        $response = wp_remote_get('https://api.openai.com/v1/chat/completions', $args);

        //print_r($response);
        if(is_wp_error($response)) {
            throw new AICompletionException($response->get_error_message());
        }

        $apiCallError = $this->openai_response_error($response);
        if(isset($apiCallError)) {
            throw new AICompletionException($apiCallError, $body, $response);
        }

        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true); // true converte l'oggetto in un array associativo

        $completion = $data['choices'][0]['message']['content'];
        if (isset($completion)) {
            return $completion; //Success
        }
        else {
            throw new AICompletionException("Invalid response, cannot find the completion content.", $body, $response);
        }
    }

    /**
     * Performs an image generation request to the AI
     * @throws AICompletionException
     */
    public function perform_image_completion(string $prompt)
    {
        $body = array(
            "model" => $this->imageCompletionsModel,
            "prompt" => $prompt,
            "n" => 1,
            "size" => $this->imageCompletionSize,
        );

        if($this->imageCompletionHDQuality) {
            $body["quality"] = "hd";
        }

        $args = array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ),
            'method' => 'POST',
            'body' => json_encode($body)
        );

        $response = wp_remote_get('https://api.openai.com/v1/images/generations', $args);

        if(is_wp_error($response)) {
            throw new AICompletionException($response->get_error_message());
        }

        $apiCallError = $this->openai_response_error($response);
        if(isset($apiCallError)) {
            throw new AICompletionException($apiCallError, $body, $response);
        }

        $response_body = wp_remote_retrieve_body($response);

        $data = json_decode($response_body, true); // true converte l'oggetto in un array associativo
        if (isset($data['data'][0]['url'])) {
            return $data['data'][0]['url'];
        } else {
            throw new AICompletionException("Error decoding response from OpenAI api call");
        }
    }


    /**
     * @param $response
     * @return string the error description contained in response if the response contains an error, otherwise returns null
     */
    private function openai_response_error($response) {
        $response_body = wp_remote_retrieve_body($response);

        $data = json_decode($response_body, true); // true converte l'oggetto in un array associativo

        // Returns the error if any
        if ($data && isset($data['error']['message'])) {
            return $data['error']['message'];
        }
        else if(isset($response['response']['code']) && isset($response['response']['message'])) {
            $code = $response['response']['code'];
            if($code >= 200 && $code <= 299) {
                return null;
            }

            return $response['response']['code'] . " - " . $response['response']['message'];
        }
        else {
            return null;
        }
    }
}

