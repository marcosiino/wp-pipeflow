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

    public function __construct(mixed $parameterA, mixed $parameterB, mixed $resultParameter)
    {
        $this->parameterA = $parameterA;
        $this->parameterB = $parameterB;
        $this->resultParameter = $resultParameter;
    }

    public function execute(PipelineContext $context): PipelineContext
    {
        $operandA = $this->getInputValue($this->parameterA, $context);
        $operandB = $this->getInputValue($this->parameterB, $context);
        $resultParameter = $this->getInputValue($this->resultParameter, $context);
        $context->setParameter($resultParameter, $operandA + $operandB);
        return $context;
    }
}