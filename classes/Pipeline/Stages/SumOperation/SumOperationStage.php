<?php

namespace Pipeline\Stages\SumOperation;
require_once PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractPipelineStage.php";

use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\PipelineContext;
use Pipeline\StageDescriptor;

class SumOperationStage implements AbstractPipelineStage
{

    private string $parameterA;
    private string $parameterB;
    private string $resultParameter;

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