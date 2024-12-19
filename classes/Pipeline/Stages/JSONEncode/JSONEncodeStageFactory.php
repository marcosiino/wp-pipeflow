<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Utils/Helpers.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageConfiguration/StageConfiguration.php";

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/JSONEncode/JSONEncodeStage.php";

class JSONEncodeStageFactory implements AbstractStageFactory
{
    public function getStageDescriptor(): StageDescriptor
    {
        $stageDescription = "Encodes a JSON from an associative array saved in the given context parameter. The JSON is then saved a string in the context parameter specified in the resultTo setting.";

        // Setup Parameters
        $setupParams = array(
            "associativeArray" => "(required) the name of the context parameter containing the associative array to encode to JSON.",
            "resultTo" => "(required) The output context parameter where the encoded JSON string is saved,"
        );

        // Context inputs
        $contextInputs = array();

        // Context outputs
        $contextOutputs = array(
            "" => "The encoded JSON string is saved in the context parameter specified in the resultTo setting",
        );

        return new StageDescriptor("JSONEncode", $stageDescription, $setupParams, $contextInputs, $contextOutputs);
    }

    /**
     * @throws StageConfigurationException
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        // TODO validate $configuration to check if it contains all the required fields
        return new JSONEncodeStage($configuration);
    }
}