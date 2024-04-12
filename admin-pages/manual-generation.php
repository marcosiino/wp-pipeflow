<?php
require_once(PLUGIN_PATH . 'ai-generation-utils/article-generation.php');

/**
 * Funzione per visualizzare la pagina di generazione dei contenuti
 */
function manual_generation_admin_page() {
    echo '<div class="wrap"><h2>PagineDaColorare.it AI Bot</h2>';

    // Se il pulsante di generazione è stato premuto
    if (isset($_POST['action']) && $_POST['action'] == 'generate') {
        $topic = $_POST['topic'];
        generateNewArticle($topic);
    }

    // Pulsante di generazione
    echo '<form method="post">';
    echo '<label for=\"topic\">Coloring Page Topic:</label><br/>';
    echo '<textarea name="topic" rows="5" cols="50">' . getRandomTopic() . '</textarea>';
    echo '<input type="hidden" name="action" value="generate">';
    submit_button('Generate');
    echo '</form>';

    echo '</div>';
}
