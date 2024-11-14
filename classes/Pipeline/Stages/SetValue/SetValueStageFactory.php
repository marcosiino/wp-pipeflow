<?php

namespace Pipeline\Stages\SetValue;

require_once PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once PLUGIN_PATH . "classes/Pipeline/Utils/Helpers.php";
require_once PLUGIN_PATH . "classes/Pipeline/Stages/SetValue/SetValueStage.php";
require_once PLUGIN_PATH . "classes/Pipeline/StageConfiguration/StageConfiguration.php";

use Pipeline\Exceptions\StageConfigurationException;
use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\Interfaces\AbstractStageFactory;
use Pipeline\StageDescriptor;
use Pipeline\Stages\SetValue\SetValueStage;
use Pipeline\Utils\Helpers;
use Pipeline\StageConfiguration\StageConfiguration;

class SetValueStageFactory implements AbstractStageFactory
{
    public function getStageDescriptor(): StageDescriptor
    {
        $stageDescription = "Sets the specified value into a context's parameter with the specified name.";

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
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        // TODO validate $configuration to check if it contains all the required fields
        return new SetValueStage($configuration);
    }
}