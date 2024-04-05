<?php

/**
 * Image Generation with DALL-E
 *
 * @param $topic
 * @return void
 */

function generateImage($topic) {

    $body = array(
        "model" => "dall-e-2",
        "prompt" => "Disegno da colorare per bambini su " . $topic . ", minimale e semplice in bianco e nero e con sole linee.",
        "n" => 1,
        "size" => "512x512",
    );

    $args = array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . get_option('paginedacolorare_ai_openai_api_key'),
        ),
        'method' => 'POST',
        'body' => json_encode($body)
    );
    $response = wp_remote_get('https://api.openai.com/v1/images/generations', $args);

    if(is_wp_error($response)) {
        die("wp_remote_get error: " . $response);
        return false;
    }

    $generated_image_url = get_image_url_from_response($response);

    if(isset($generated_image_url)) {
        $saved_image_id = download_image($generated_image_url, $topic);
        if(is_wp_error($saved_image_id)) {
            die("download_and_attach_image error: " . $saved_image_id);
            return false;
        }
        return $saved_image_id;
    }
    else {
        die("didn't find generated image url in body: " . $response);
        return false;
    }
}