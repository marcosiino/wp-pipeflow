<?php

namespace Pipeline\Stages\SetValue;
require_once PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractPipelineStage.php";

use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\PipelineContext;
use Pipeline\StageDescriptor;

class SetValueStage extends AbstractPipelineStage
{
    private string $parameterName;
    private mixed $parameterValue;

    public function __construct(mixed $parameterName, mixed $parameterValue)
    {
        $this->parameterName = $parameterName;
        $this->parameterValue = $parameterValue;
    }

    public function execute(PipelineContext $context): PipelineContext
    {
        $parameterName = $this->parameterName;
        $parameterValue = $this->parameterValue;
        $context->setParameter($parameterName, $parameterValue);
        return $context;
    }
}