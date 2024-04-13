<?php

function insert_post(string $title, string $content, array $category_ids, array $tags_ids, $thumbnail_image_id, $status = 'publish') {
    // Prepara i dati del post
    $post_data = array(
        'post_title'    => $title, // Titolo dell'articolo
        'post_content'  => $content, // Contenuto dell'articolo
        'post_status'   => $status, // Stato del post. Usa 'draft' per un bozza o 'publish' per pubblicarlo immediatamente.
        'post_type'     => 'post', // Tipo di post. Usa 'post' per un articolo standard o 'page' per una pagina.
    );

    // Inserisce il post e ottiene l'ID del post inserito
    $post_id = wp_insert_post($post_data);

    if(isset($category_ids) && count($category_ids) > 0) {
        wp_set_post_categories($post_id, $category_ids);
    }

    if(isset($tags_ids) && count($tags_ids) > 0) {
        wp_set_post_terms($post_id, $tags_ids, 'post_tag');
    }

    // Controlla se l'inserimento ha avuto successo e imposta l'immagine in evidenza
    if (!is_wp_error($post_id)) {
        set_post_thumbnail($post_id, $thumbnail_image_id);
        return $post_id;
    } else {
        // Gestisce l'errore
        return $post_id->get_error_message();
    }
}

function get_available_categories() {
    $categories = get_categories(array(
        'hide_empty' => false,
        'type' => 'post'
    ));
    $cats_array = array();

    foreach($categories as $category) {
        $cats_array[] = array(
            'id' => $category->term_id,
            'name' => $category->name,
        );
    }

    return $cats_array;
}

function get_available_tags() {
    $tags = get_tags(array(
        'hide_empty' => false,
        'type' => 'post'
    ));
    $tags_array = array();

    foreach($tags as $tag) {
        $tags_array[] = array(
            'id' => $tag->term_id,
            'name' => $tag->name,
        );
    }

    return $tags_array;
}