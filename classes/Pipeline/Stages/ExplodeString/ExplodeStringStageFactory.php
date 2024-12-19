<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Utils/Helpers.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageConfiguration/StageConfiguration.php";

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/ExplodeString/ExplodeStringStage.php";

class ExplodeStringStageFactory implements AbstractStageFactory
{
    public function getStageDescriptor(): StageDescriptor
    {
        $stageDescription = "Splits a string by using a string separator and returns an array with the splitted strings.";

        // Setup Parameters
        $setupParams = array(
            "inputString" => "(required) The string to split.",
            "separator" => "(required) The separator string.",
            "resultTo" => "(required, array) The output context parameter where the array of splitted string is saved",
        );

        // Context inputs
        $contextInputs = array();

        // Context outputs
        $contextOutputs = array(
            "" => "The array of splitted strings is saved into the context parameter specified in resultTo setting.",
        );

        return new StageDescriptor("ExplodeString", $stageDescription, $setupParams, $contextInputs, $contextOutputs);
    }

    /**
     * @throws StageConfigurationException
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        // TODO validate $configuration to check if it contains all the required fields
        return new ExplodeStringStage($configuration);
    }
}