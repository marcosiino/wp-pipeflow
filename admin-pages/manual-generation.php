<?php

require_once PLUGIN_PATH . "classes/Pipeline/Exceptions/StageConfigurationException.php";
require_once PLUGIN_PATH . "classes/Pipeline/Exceptions/PipelineExecutionException.php";
require_once PLUGIN_PATH . "classes/Pipeline/Pipeline.php";
require_once PLUGIN_PATH . "classes/Pipeline/PipelineContext.php";

use Pipeline\Exceptions\PipelineExecutionException;
use Pipeline\Pipeline;
use Pipeline\Exceptions\StageConfigurationException;
use Pipeline\PipelineContext;

require_once(PLUGIN_PATH . 'classes/ArticleGenerator.php');

/**
 * Funzione per visualizzare la pagina di generazione dei contenuti
 */
function manual_generation_admin_page() {
    echo '<div class="wrap"><h2>On-Demand Content Generator</h2>';
    echo '<form method="post">';
    echo '<label for=\"pipelineConfiguration\">Set up the content generation pipeline below:</label><br/>';
    echo '<textarea name="pipelineConfiguration" rows="10" cols="70">' . Settings::get_pipeline_configuration_json() . '</textarea>';
    echo '<input type="hidden" name="action" value="launchPipeline">';
    submit_button('Execute the Pipeline');
    echo '</form>';
    echo '</div>';

    if (isset($_POST['action']) && $_POST['action'] == 'launchPipeline') {
        $pipelineConfiguration = wp_unslash($_POST['pipelineConfiguration']);
        launchPipeline($pipelineConfiguration);
    }
}

/**
 * @throws \Pipeline\Exceptions\StageConfigurationException
 */
function launchPipeline($pipelineConfiguration) {
    try {
        $initialContext = new PipelineContext();
        $initialContext->setParameter("OPENAI_API_KEY", Settings::get_openAI_api_key());

        $pipeline = new Pipeline($initialContext);
        //$pipeline->setup($pipelineConfiguration);
        $pipeline->setupWithXML($pipelineConfiguration);
        $outputContext = $pipeline->execute();

        printContext($outputContext->getHTMLDescription());
    }
    catch(StageConfigurationException $configException)
    {
        printError($configException->getMessage());
    }
    catch(PipelineExecutionException $executionException)
    {
        printError($executionException->getMessage());
    }
}

function printError($error) {
    echo "<div class='pipeline-error'>";
    echo "<p style=\"color: red;\">$error</p>";
    echo "</div>";
}

function printContext($context) {
    echo "<div class='pipeline-context'>";
    echo "<h3>Output Context</h3>";
    echo $context;
    echo "</div>";
}
