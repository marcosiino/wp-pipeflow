<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Utils/Helpers.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageConfiguration/StageConfiguration.php";

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/RandomValue/RandomValueStage.php";

class RandomValueStageFactory implements AbstractStageFactory
{
    public function getStageDescriptor(): StageDescriptor
    {
        $stageDescription = "Sets the specified value into a context's parameter with the specified name.";

        // Setup Parameters
        $setupParams = array(
            "parameterName" => "(required) The name of the context parameter where the generated random value is saved.",
            "minValue" => "The minimum random value.",
            "maxValue" => "The maximum random value.",
        );

        // Context inputs
        $contextInputs = array();

        // Context outputs
        $contextOutputs = array(
            "" => "A random value which is saved into the context (the context parameter name is specified in parameterName).",
        );

        return new StageDescriptor("RandomValue", $stageDescription, $setupParams, $contextInputs, $contextOutputs);
    }

    /**
     * @throws StageConfigurationException
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        // TODO validate $configuration to check if it contains all the required fields
        return new RandomValueStage($configuration);
    }
}