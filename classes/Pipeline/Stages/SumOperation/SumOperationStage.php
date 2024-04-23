<?php

namespace Pipeline\Stages\SumOperation;
require_once "classes/Pipeline/Interfaces/AbstractPipelineStage.php";

use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\PipelineContext;
use Pipeline\StageDescriptor;

class SumOperationStage implements AbstractPipelineStage
{

    private string $parameterA;
    private string $parameterB;
    private string $resultParameter;

    static public function getDescriptor(): StageDescriptor
    {
        $stageDescription = "Sums the value of the parameter specified in PARAMETER_A with the value of the parameter specified in PARAMETER_B and stores the result into the parameter specified in RESULT_PARAMETER";

        $inputs = array(
            "PARAMETER_A" => "The operand A parameter for the sum operation",
            "PARAMETER_B" => "The operand B parameter for the sum operation",
            "RESULT_PARAMETER" => "The name of the parameter where the result is stored",
        );

        $outputs = array();

        return new StageDescriptor("SumOperation", $stageDescription, $inputs, $outputs);
    }

    public function __construct(string $parameterA, string $parameterB, string $resultParameter)
    {
        $this->parameterA = $parameterA;
        $this->parameterB = $parameterB;
        $this->resultParameter = $resultParameter;
    }

    public function execute(PipelineContext $context): PipelineContext
    {
        $operandA = $context->getParameter($this->parameterA)->getLast();
        $operandB = $context->getParameter($this->parameterB)->getLast();
        $result = $operandA + $operandB;

        $context->setParameter($this->resultParameter, $result);
        return $context;
    }
}