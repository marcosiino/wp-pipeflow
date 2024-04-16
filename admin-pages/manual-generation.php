<?php
require_once(PLUGIN_PATH . 'classes/ArticleGenerator.php');

/**
 * Funzione per visualizzare la pagina di generazione dei contenuti
 */
function manual_generation_admin_page() {
    echo '<div class="wrap"><h2>PostBrewer AI Assistant</h2>';

    // Se il pulsante di generazione è stato premuto
    if (isset($_POST['action']) && $_POST['action'] == 'generate') {
        $topic = $_POST['topic'];
        $articleGenerator = new ArticleGenerator();
        $articleGenerator->generate_new_article($topic);
    }

    // Pulsante di generazione
    echo '<form method="post">';
    echo '<label for=\"topic\">Topic:</label><br/>';
    echo '<textarea name="topic" rows="5" cols="50">' . ArticleGenerator::get_random_topic() . '</textarea>';
    echo '<input type="hidden" name="action" value="generate">';
    submit_button('Generate');
    echo '</form>';

    echo '</div>';
}
