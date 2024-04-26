<?php

namespace Pipeline\Stages\SumOperation;

require_once PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once PLUGIN_PATH . "classes/Pipeline/Utils/Helpers.php";
require_once PLUGIN_PATH . "classes/Pipeline/Stages/SumOperation/SumOperationStage.php";
require_once PLUGIN_PATH . "classes/Pipeline/StageConfiguration/StageConfiguration.php";

use Pipeline\Exceptions\StageConfigurationException;
use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\Interfaces\AbstractStageFactory;
use Pipeline\StageDescriptor;
use Pipeline\Stages\SumOperation\SumOperationStage;
use Pipeline\Utils\Helpers;
use Pipeline\StageConfiguration\StageConfiguration;

class SumOperationStageFactory implements AbstractStageFactory
{
    public function getStageDescriptor(): StageDescriptor
    {
        $stageDescription = "Sums two context's parameters and stores the result into the specified context's parameter";

        // Setup Parameters
        $setupParams = array(
            "operandA" => "The name of the context's parameter to be used as the first operand of the operation",
            "operandB" => "The name of the context's parameter to be used as the second operand of the operation",
            "resultTo" => "The name of the context's parameter to be used to store the result of the operation",
        );

        // Context inputs
        $contextInputs = array();

        // Context outputs
        $contextOutputs = array(
            "SUM_RESULT" => "The result of the operation. If resultTo is set, the result of the operation is written in the context parameter specified in that setting instead.",
        );

        return new StageDescriptor("SumOperation", $stageDescription, $setupParams, $contextInputs, $contextOutputs);
    }

    /**
     * @throws StageConfigurationException
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        //TODO: validate $configuration
        return new SumOperationStage($configuration);
    }
}