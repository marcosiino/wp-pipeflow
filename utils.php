<?php

/**
 * Download and save an image to the media library and eventually associate it to a post if post_id != 0
 *
 * @param $image_url
 * @param $post_id
 * @return mixed
 */
function download_image($image_url, $image_description) {
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    // Scarica l'immagine
    $temp_file = download_url($image_url);

    // Estrai l'estensione dell'URL dell'immagine
    $image_ext = pathinfo(parse_url($image_url, PHP_URL_PATH), PATHINFO_EXTENSION);

    // Se l'estensione è stata trovata, rinomina il file temporaneo
    if ($image_ext) {
        $new_file_path = $temp_file . '.' . $image_ext;
        rename($temp_file, $new_file_path);
        $temp_file = $new_file_path;
    }
    else {
        return null;
    }

    //die($temp_file);

    if (is_wp_error($temp_file)) {
        die("error downloading image to temporary file: " . $image_url);
        return $temp_file; // Ritorna l'errore se c'è stato un problema con il download
    }

    // Imposta il nome del file e l'array per `media_handle_sideload()`
    $file = array(
        'name' => basename($temp_file), // Usa il nome originale dell'immagine
        'tmp_name' => $temp_file, // Percorso al file temporaneo
    );

    // Carica l'immagine nei media
    $id = media_handle_sideload($file);

    // Controlla se c'è stato un errore
    if (is_wp_error($id)) {
        @unlink($file['tmp_name']); // Cancella il file temporaneo in caso di errore
        die("error saving image to the library: " . $id->get_error_message());
        return $id; // Ritorna l'errore
    }

    // Ritorna l'ID dell'immagine caricata nei media
    return $id;
}

/**
 * @param $response
 * @return Returns the error description contained in response if the response contains an error, otherwise returns null
 */
function is_openai_response_error($response) {
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

function insert_post($title, $content, $thumbnail_image_id, $status = 'publish') {
    // Prepara i dati del post
    $post_data = array(
        'post_title'    => $title, // Titolo dell'articolo
        'post_content'  => $content, // Contenuto dell'articolo
        'post_status'   => $status, // Stato del post. Usa 'draft' per un bozza o 'publish' per pubblicarlo immediatamente.
        'post_type'     => 'post', // Tipo di post. Usa 'post' per un articolo standard o 'page' per una pagina.
    );

    // Inserisce il post e ottiene l'ID del post inserito
    $post_id = wp_insert_post($post_data);

    // Controlla se l'inserimento ha avuto successo e imposta l'immagine in evidenza
    if (!is_wp_error($post_id)) {
        set_post_thumbnail($post_id, $thumbnail_image_id);
        return $post_id;
    } else {
        // Gestisce l'errore
        return $post_id->get_error_message();
    }
}