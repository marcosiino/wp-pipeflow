<?php

namespace Pipeline\Stages\SumOperation;

require_once PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once PLUGIN_PATH . "classes/Pipeline/Utils/Helpers.php";
require_once PLUGIN_PATH . "classes/Pipeline/Stages/SumOperation/SumOperationStage.php";

use Pipeline\Exceptions\StageConfigurationException;
use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\Interfaces\AbstractStageFactory;
use Pipeline\StageDescriptor;
use Pipeline\Stages\SumOperation\SumOperationStage;
use Pipeline\Utils\Helpers;

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
            "" => "A parameter with the name specified in the *resultParameter* setup parameter which contains the result of the operation",
        );

        return new StageDescriptor("SumOperation", $stageDescription, $setupParams, $contextInputs, $contextOutputs);
    }

    /**
     * @throws StageConfigurationException
     */
    public function instantiate(array $configuration): AbstractPipelineStage
    {
        $operandA = Helpers::getField($configuration, "operandA", true);
        $operandB = Helpers::getField($configuration, "operandB", true);
        $resultTo = Helpers::getField($configuration, "resultTo", true);
        return new SumOperationStage($operandA, $operandB, $resultTo);
    }
}