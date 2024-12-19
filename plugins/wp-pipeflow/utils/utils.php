<?php
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Exceptions/StageConfigurationException.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Exceptions/PipelineExecutionException.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Pipeline.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/PipelineContext.php";


function launchPipeline($pipelineConfiguration, $output = true) {
    try {
        $initialContext = new PipelineContext();

        $pipeline = new Pipeline($initialContext);
        //$pipeline->setup($pipelineConfiguration);
        $pipeline->setupWithXML($pipelineConfiguration);
        $outputContext = $pipeline->execute();

        if($output == true) {
            printContext($outputContext->getHTMLDescription());
        }
    }
    catch(StageConfigurationException $configException)
    {
        if($output == true) {
            printError($configException->getMessage());
        }
    }
    catch(PipelineExecutionException $executionException)
    {
        if($output == true) {
            printError($executionException->getMessage());
        }
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
