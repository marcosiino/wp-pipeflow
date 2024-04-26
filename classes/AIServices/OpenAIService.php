<?php

namespace AIServices;
require_once PLUGIN_PATH . "classes/AIServices/AICompletionException.php";
require_once PLUGIN_PATH . "classes/AIServices/AICompletionServiceInterface.php";

// The istructions for the text completion json return format
define('AUTO_CATEGORIES_INSTRUCTIONS', "Following a list of categories and tags available, described with a json which contains id and name fields.   

*AVAILABLE CATEGORIES:* 
%CATEGORIES%

*AVAILABLE TAGS*:
%TAGS%

*INSTRUCTIONS*
You have to read the text provided after the *TEXT CONTENT* line, and return a minimum of 0 and a maximum of %N_MAX_CATEGORIES% categories and a minimum of 0 and a maximum of %N_MAX_TAGS% tags by choosing the most appropriate categories and tags for the text provided after the *TEXT CONTENT* line.
You must return only a valid json, without backticks and without any other formatting. The json must contain a \"categories_ids\" fields which contains an array of ids of the choosen categories, and a field \"tags_ids\" which contains an array of the ids of the choosen tags. If you didn't find any appropriate category or tag, provide an empty array.

*TEXT CONTENT*
%TEXT%
");

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

    public function perform_text_completion(string $prompt, bool $return_json_response, string $image_attachment_url = null, float $temperature = 0.7, int $max_tokens = 4096)
    {
        $content = array(
            array("type" => "text", "text" => $prompt)
        );

        if (isset($image_attachment_url)) {
            $content[] = array(
                "type" => "image_url",
                "image_url" => array("url" => $image_attachment_url)
            );
        }

        $body = array(
            "model" => $this->textCompletionsModel,
            "messages" => array(array("role" => "user", "content" => $content)),
            "temperature" => $temperature,
            "max_tokens" => $max_tokens
        );

        if ($return_json_response) {
            $body["response_format"] = array("type" => "json_object");
        }

        $response = $this->send_request('https://api.openai.com/v1/chat/completions', 'POST', $body);

        if ($response['error']) {
            throw new AICompletionException($response['error_message']);
        }

        $data = $response['body'];
        $finish_reason = $data["finish_reason"] ?? null;
        if ($return_json_response && $finish_reason == "length") {
            throw new AICompletionException("The completion took more than the max_tokens provided, and since return_json_response is true, the call is throwing because the returned json completion is not complete and will be not deserializable");
        }

        $completion = $data['choices'][0]['message']['content'] ?? null;
        if ($completion) {
            return $completion;
        } else {
            throw new AICompletionException("Invalid response, cannot find the completion content.");
        }
    }

    public function perform_image_completion(string $prompt, int $count = 1)
    {
        $body = array(
            "model" => $this->imageCompletionsModel,
            "prompt" => $prompt,
            "n" => $count,
            "size" => $this->imageCompletionSize,
            "quality" => $this->imageCompletionHDQuality == true ? "hd" : "standard"
        );

        print_r($body);
        $response = $this->send_request('https://api.openai.com/v1/images/generations', 'POST', $body);

        if ($response['error']) {
            throw new AICompletionException($response['error_message']);
        }

        $decodedJSON = $response['body'];
        if (isset($decodedJSON["data"])) {
            $generatedImagesURLs = array_map(function ($image) {
                return $image['url'];
            }, $decodedJSON['data']);
            return $generatedImagesURLs;
        } else {
            throw new AICompletionException("Error decoding response from OpenAI api call");
        }
    }

    private function send_request($url, $method, $body)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['error' => true, 'error_message' => $err];
        } else {
            $decoded_response = json_decode($response, true);
            if (isset($decoded_response['error'])) {
                return ['error' => true, 'error_message' => $decoded_response['error']['message']];
            }
            return ['error' => false, 'body' => $decoded_response];
        }
    }
}

