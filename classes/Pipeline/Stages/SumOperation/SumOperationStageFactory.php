<?php

namespace Pipeline\Stages\SumOperation;

require_once "classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once "classes/Pipeline/Utils/Helpers.php";
require_once "classes/Pipeline/Stages/SumOperation/SumOperationStage.php";

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
            "parameterA" => "The name of the context's parameter to be used as the first operand of the operation",
            "parameterB" => "The name of the context's parameter to be used as the second operand of the operation",
            "resultParameter" => "The name of the context's parameter to be used to store the result of the operation",
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
        $parameterA = Helpers::getField($configuration, "parameterA", true);
        $parameterB = Helpers::getField($configuration, "parameterB", true);
        $resultParameter = Helpers::getField($configuration, "resultParameter", true);
        return new SumOperationStage($parameterA, $parameterB, $resultParameter);
    }
}