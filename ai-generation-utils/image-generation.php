<?php
require_once(PLUGIN_PATH . "utils/utils.php");

/**
 * Generates a prompt for coloring pages image generation, by using the specified topic
 */
function generatePrompt($image_topic) {
    $prompt_template = get_option('image_generation_prompt'); // Get the image generation prompt set by the user

    $result = str_replace("%TOPIC%", $image_topic, $prompt_template);
    return $result;
}

/**
 * Image Generation with input prompt parameters
 *
 * @param $input_params: an array of dictionaries of type "key" => "value" which contains the parameters and their values to be replaced into the prompt. For example by passing this dictionary: "topic" => "A topic", each occurrence of %TOPIC% in the prompt is replaced with the param value ("A topic" in the example).
 * @return a dictionary containing:
 *      - image_id: the generated image url
 *      - image_url: the id of the image saved to wordpress library
 * or null on failure
 */

function generate_image($input_params = array()) {
    $prompt = get_option('image_generation_prompt'); // Get the image generation prompt set by the user
    $prompt = prompt_with_inputs($prompt, $input_params);

    return generate_image_with_prompt($prompt);
}

/**
 * Image Generation with DALL-E with a prompt
 *
 * @param $prompt: the prompt
 * @return a dictionary containing:
 *      - image_id: the generated image url
 *      - image_url: the id of the image saved to wordpress library
 * or null on failure
 */
function generate_image_with_prompt($prompt) {
    $body = array(
        "model" => "dall-e-3",
        "prompt" => $prompt,
        "n" => 1,
        "size" => "1024x1024",
        "quality" => "hd",
    );

    $args = array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . get_option('paginedacolorare_ai_openai_api_key'),
        ),
        'method' => 'POST',
        'body' => json_encode($body)
    );

    echo "<p>Generating image with prompt: " . $prompt . "</p>";

    $response = wp_remote_get('https://api.openai.com/v1/images/generations', $args);

    if(is_wp_error($response)) {
        echo "<p>OpenAI api call failed</p>";
        return null;
    }

    $apiCallError = is_openai_response_error($response);
    if(isset($apiCallError)) {
        echo "<p>OpenAI api call for image generation failed: " . $apiCallError . "</p>";
        echo "<textarea cols='15' rows='15'><pre>";
        print_r($response);
        echo "</pre></textarea>";
        return null;
    }

    $generated_image_data = get_image_data_from_response($response);
    if(isset($generated_image_data)) {
        $saved_image_id = download_image($generated_image_data['image_url']);
        $image_url = $generated_image_data['image_url'];

        if(is_wp_error($saved_image_id)) {
            return null;
        }
        return array(
            'image_id' => $saved_image_id,
            'image_url' => $image_url,
        );
    }
    else {
        echo "<p>Cannot get image data from response</p>";
        return null;
    }
}

function get_image_data_from_response($response) {
    $response_body = wp_remote_retrieve_body($response);

    // Decodifica il JSON
    $data = json_decode($response_body, true); // true converte l'oggetto in un array associativo
    // Verifica se la decodifica è riuscita e se l'elemento 'data' esiste
    if ($data && isset($data['data'][0]['url'])) {
        $image_url = $data['data'][0]['url'];
        $revised_prompt = $data['data'][0]['revised_prompt']; //optional, may be not available

        // Ora hai l'URL dell'immagine e puoi procedere con il download o altre operazioni
        return array(
            "image_url" => $image_url,
            "revised_prompt" => $revised_prompt,
        );
    } else {
        // Gestisci l'errore se la struttura dei dati non è quella prevista o se la decodifica fallisce
        echo "<p>Error decoding response from OpenAI api call</p>";
        return null;
    }
}