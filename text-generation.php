<?php

/**
 * Generates a prompt for coloring pages image generation, by using the specified topic
 */
function generateTextPrompt($image_url) {
    return "Scrivi una descrizione ricca da 200 parole e un titolo per il disegno da colorare fornito. Il testo deve essere ottimizzato per il SEO, con parole come \"disegni da colorare\" incluse le parole chiave inerenti al disegno nello specifico. Anche il titolo deve contenere la parola \"disegno da colorare\" unita al tipo di disegno specifico. Le parole chiave importanti racchiudile con il tag <strong></strong>. Il formato della risposta deve rigorosamente essere un json valido, senza backticks ne altra formattazione, e con i campi title e description che indicano rispettivamente il titolo e la descrizione del disegno.";
}

/**
 * Coloring page Image Generation with DALL-E
 *
 * @param $topic: the coloring page image topic
 * @return a dictionary containing:
 *      - image_url: the generated image url
 *      - revised_prompt: the revised prompt
 * or null on failure
 */

function generateText($image_url) {

    $prompt = generateTextPrompt($image_url);

    $body = array(
        "model" => "gpt-4-vision-preview",
        "messages" => [
            array(
                "role" => "user",
                "content" => [
                    array(
                        "type" => "text",
                        "text" => $prompt,
                    ),
                    array(
                        "type" => "image_url",
                        "image_url" => $image_url,
                    )
                ],
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
    echo "<p>Image URL for vision analysis: " . $image_url . "</p>";

    $response = wp_remote_get('https://api.openai.com/v1/chat/completions', $args);

    if(is_wp_error($response)) {
        echo "<p>OpenAI api call failed: " . $response->get_error_message() . "</p>";
        return null;
    }

    $apiCallError = is_openai_response_error($response);
    if(isset($apiCallError)) {
        echo "<p>OpenAI api call for text completion failed: " . $apiCallError . "</p>";
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
            return array(
                "title" => $title,
                "description" => $description,
            );
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