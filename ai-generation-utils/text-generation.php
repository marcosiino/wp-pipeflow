<?php
require_once(PLUGIN_PATH . "utils/defaults.php");
require_once(PLUGIN_PATH . "utils/utils.php");


/**
 * Gets the prompt for text generation from settings and adds input parameters
 */
function generateTextPrompt($input_params) {
    $prompt = get_option('article_generation_prompt', TEXT_DEFAULT_PROMPT);
    $prompt .= JSON_COMPLETION_FORMAT_INSTRUCTIONS;

    $final_params = array(
    /*    array(
            "key" => "CATEGORIES",
            "value" => json_encode(get_available_categories())
        ),
        array(
            "key" => "TAGS",
            "value" => json_encode(get_available_tags())
        ),*/
    );

    if(isset($input_params)) {
        foreach ($input_params as $param) {
            array_push($final_params, $param);
        }
    }

    $prompt = prompt_with_inputs($prompt, $final_params);
    return $prompt;
}

/**
 * Coloring page Image Generation with DALL-E
 *
 * @param $topic: the coloring page image topic
 * @return a dictionary containing generated text data (see get_text_completion_data_from_response result dictionary)
 * or null on failure
 */

function generate_text($input_params = array(), $attached_image_url) {

    $prompt = generateTextPrompt($input_params);

    $model = "gpt-4-turbo";

    $content = array();
    $content[] = array(
        "type" => "text",
        "text" => $prompt,
    );

    // Adds the image url to the content and switch to "vision"
    if(isset($attached_image_url)) {
        $content[] = array(
            "type" => "image_url",
            "image_url" => array(
                "url" => $attached_image_url,
            )
        );
    }

    $body = array(
        "model" => $model,
        "messages" => [
            array(
                "role" => "user",
                "content" => $content,
            )
        ],
        "temperature" => 0.7,
        "max_tokens" => 4096,
    );

    $args = array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . get_option('paginedacolorare_ai_openai_api_key'),
        ),
        'method' => 'POST',
        'body' => json_encode($body)
    );

    echo "<p>Generating text completion with prompt: " . $prompt . "</p>";
    echo "<p>Image URL for vision analysis: " . $attached_image_url . "</p>";

    $response = wp_remote_get('https://api.openai.com/v1/chat/completions', $args);

    //print_r($response);
    if(is_wp_error($response)) {
        echo "<p>OpenAI api call failed: " . $response->get_error_message() . "</p>";
        return null;
    }

    $apiCallError = is_openai_response_error($response);
    if(isset($apiCallError)) {
        echo "<p>OpenAI api call for text completion failed: " . $apiCallError . "</p>";
        echo "<textarea cols='50' rows='50'>";
        print_r($body);
        echo "</textarea>";
        echo "<textarea cols='50' rows='50'>";
        print_r($response);
        echo "</textarea>";
        return null;
    }

    $generated_data = get_text_completion_data_from_response($response);
    if(isset($generated_data)) {
        return $generated_data;
    }
    else {
        echo "<p>Cannot get text completion data from response</p>";
        return null;
    }
}

/**
 * @param $response
 * @return null on failure, or a dictionary with:
 *  - title: the title of the generated article
 *  - description: the description of the generated article
 *  - category_ids: an array with the most appropriate categories' ids for the article
 *  - tag_ids: an array with the most appropriate tags' ids for the article
 */
function get_text_completion_data_from_response($response) {
    $response_body = wp_remote_retrieve_body($response);

    // Decodifica il JSON
    $data = json_decode($response_body, true); // true converte l'oggetto in un array associativo
    // Verifica se la decodifica è riuscita e se l'elemento 'data' esiste
    $completion_message = $data['choices'][0]['message'];
    if (isset($completion_message) && isset($completion_message['content'])) {
        $decoded_content = json_decode($completion_message['content'], true);
        $title = $decoded_content['title'];
        $description = $decoded_content['description'];

        if(isset($decoded_content) && isset($title) && isset($description)) {
            echo "<p>The returned completion is formed correctly as json and has been decoded. The title is: \"" . $title . "\"</p>";
            $result = array(
                "title" => $title,
                "description" => $description,
            );

            return $result;
        }
        else {
            print_r($response);
            echo "<p>Error decoding the completion from OpenAI api call: maybe the returned completion isn\'t a json formed as described in the prompt?</p>";
            return null;
        }
    } else {
        echo "<p>Error decoding response from OpenAI api call</p>";
        return null;
    }
}