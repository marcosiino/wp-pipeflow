<?php

namespace Pipeline\Stages\SetValue;

require_once PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once PLUGIN_PATH . "classes/Pipeline/Utils/Helpers.php";
require_once PLUGIN_PATH . "classes/Pipeline/Stages/SetValue/SetValueStage.php";

use Pipeline\Exceptions\StageConfigurationException;
use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\Interfaces\AbstractStageFactory;
use Pipeline\StageDescriptor;
use Pipeline\Stages\SetValue\SetValueStage;
use Pipeline\Utils\Helpers;

class SetValueStageFactory implements AbstractStageFactory
{
    public function getStageDescriptor(): StageDescriptor
    {
        $stageDescription = "Sets the specified context's parameter to the fixed value provided.";

        // Setup Parameters
        $setupParams = array(
            "parameterName" => "The name of the parameter to which the fixed value is assigned.",
            "parameterValue" => "The fixed value to assign to the specified parameter.",
        );

        // Context inputs
        $contextInputs = array();

        // Context outputs
        $contextOutputs = array(
            "" => "A parameter with the name specified using the *parameterName* setup parameter, with the fixed value provided in the *parameterValue* setup parameter.",
        );

        return new StageDescriptor("SetValue", $stageDescription, $setupParams, $contextInputs, $contextOutputs);
    }

    /**
     * @throws StageConfigurationException
     */
    public function instantiate(array $configuration): AbstractPipelineStage
    {
        $parameterName = Helpers::getField($configuration, "parameterName", true);
        $parameterValue = Helpers::getField($configuration, "parameterValue", true);
        return new SetValueStage($parameterName, $parameterValue);
    }
}