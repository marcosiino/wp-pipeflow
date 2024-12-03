<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/utils/settings.php";

/**
 * Funzione per visualizzare la pagina di generazione dei contenuti
 */
function manual_execution_admin_page() {
    echo '<div class="wrap"><h2>Manual Pipeline execution</h2>';
    echo '<form method="post">';
    echo '<label for=\"pipelineConfiguration\">Enter the pipeline configuration below:</label><br/>';
    echo '<textarea name="pipelineConfiguration" rows="10" cols="70">' . Settings::get_pipeline_configuration() . '</textarea>';
    echo '<input type="hidden" name="action" value="launchPipeline">';
    submit_button('Execute the Pipeline');
    echo '</form>';
    echo '</div>';

    if (isset($_POST['action']) && $_POST['action'] == 'launchPipeline') {
        $pipelineConfiguration = wp_unslash($_POST['pipelineConfiguration']);
        launchPipeline($pipelineConfiguration);
    }
}