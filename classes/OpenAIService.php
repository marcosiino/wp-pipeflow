<?php
require_once(PLUGIN_PATH . "classes/AICompletionErrors.php");
require_once(PLUGIN_PATH . "classes/AICompletionServiceInterface.php");

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

    /**
     * Performs a text generation request to the AI, with an optional image attachment url
     * @returns string containing the text completion
     * @throws AICompletionException
     */
    public function perform_text_completion(string $prompt, bool $return_json_response, string $image_attachment_url = null, float $temperature = 0.7, int $max_tokens = 4096)
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

        if($return_json_response) {
            $body["response_format"] = array(
                "type" => "json_object",
            );
        }

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
        $finish_reason = $data["finish_reason"];
        if($return_json_response && $finish_reason == "length") {
            throw new AICompletionException("The completion took more than the max_tokens provided, and since return_json_response is true, the call is throwing because the returned json completion is not complete and will be not deserializable");
        }
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
     * @returns string the url of the generated image
     * @throws AICompletionException
     */
    public function perform_image_completion(string $prompt, int $count = 1)
    {
        $body = array(
            "model" => $this->imageCompletionsModel,
            "prompt" => $prompt,
            "n" => $count,
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
     * @param int $max_num_of_categories
     * @param int $max_num_of_tags
     * @return array|array[] array containing categories_ids and tags_ids keys, which contains a string with the appropriate categories ids and tags ids for the provided $content
     * @throws AICompletionException
     */
    public function perform_categories_and_tags_assignment_completion(string $content, array $available_categories, array $available_tags, $max_categories_num, $max_tags_num) {
        $params = array(
            array(
                "key" => "CATEGORIES",
                "value" => json_encode($available_categories),
            ),
            array(
                "key" => "TAGS",
                "value" => json_encode($available_tags),
            ),
            array(
                "key" => "N_MAX_CATEGORIES",
                "value" => $max_categories_num,
            ),
            array(
                "key" => "N_MAX_TAGS",
                "value" => $max_tags_num,
            ),
            array(
                "key" => "TEXT",
                "value" => $content,
            )
        );
        $prompt = $this->prompt_with_inputs(AUTO_CATEGORIES_INSTRUCTIONS, $params);
        $completion = $this->perform_text_completion($prompt, true, null,0.4, 200);

        $decodedCompletion = json_decode($completion,true);
        $categories_ids = $decodedCompletion['categories_ids'];
        $tags_ids = $decodedCompletion['tags_ids'];

        if(isset($categories_ids) && isset($tags_ids)) {
            //SUCCESS
            return array(
                "categories_ids" => $categories_ids,
                "tags_ids" => $tags_ids,
            );
        }
        else {
            return array(
                "categoryIds" => array(), //array of appropriate category ids for the generated article
                "tagIds" => array(),  //array of appropriate tags ids for the generated article
            );
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

    private function prompt_with_inputs($prompt, $input_params) {

        //Replaces the parameters in the prompt with the input parameters values provided
        if(isset($input_params)) {
            foreach($input_params as $param) {
                $prompt = str_replace("%" . strtoupper($param['key']) . "%", $param['value'], $prompt);
            }
        }

        return $prompt;
    }
}

