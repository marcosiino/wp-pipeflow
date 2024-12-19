<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Utils/Helpers.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageConfiguration/StageConfiguration.php";

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/RandomArrayItem/RandomArrayItemStage.php";

class RandomArrayItemStageFactory implements AbstractStageFactory
{
    public function getStageDescriptor(): StageDescriptor
    {
        $stageDescription = "Pick and return a random array item from the specified array context parameter.";

        // Setup Parameters
        $setupParams = array(
            "arrayParameterName" => "(required) The name of the array context parameter.",
            "resultTo" => "The name of the context parameter where the random picked element is saved.",
        );

        // Context inputs
        $contextInputs = array();

        // Context outputs
        $contextOutputs = array(
            "" => "A random item from the specified array, which is saved into the resultTo context parameter.",
        );

        return new StageDescriptor("RandomArrayItem", $stageDescription, $setupParams, $contextInputs, $contextOutputs);
    }

    /**
     * @throws StageConfigurationException
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        // TODO validate $configuration to check if it contains all the required fields
        return new RandomArrayItemStage($configuration);
    }
}