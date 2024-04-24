<?php

namespace Pipeline\Stages\SumOperation;
require_once PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractPipelineStage.php";

use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\PipelineContext;
use Pipeline\StageDescriptor;

class SumOperationStage extends AbstractPipelineStage
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
        $operandA = $this->getInputValue($this->parameterA, $context, true)[0];
        $operandB = $this->getInputValue($this->parameterB, $context, true)[0];

        $context->setParameter($this->resultParameter, $operandA + $operandB);
        return $context;
    }
}