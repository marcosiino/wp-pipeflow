<?php

namespace Pipeline\Stages\SetValue;
require_once "classes/Pipeline/Interfaces/AbstractPipelineStage.php";

use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\PipelineContext;
use Pipeline\StageDescriptor;

class SetValueStage implements AbstractPipelineStage
{

    private string $parameterName;
    private mixed $parameterValue;

    static public function getDescriptor(): StageDescriptor
    {
        $stageDescription = "Sets a parameter of the context to the fixed value provided";

        $inputs = array(
            "PARAMETER_NAME" => "The name of the context's parameter that will be set to the given value",
            "VALUE" => "The fixed value that will be set for the specified context's parameter"
        );

        $outputs = array();

        return new StageDescriptor("SetValue", $stageDescription, $inputs, $outputs);
    }

    public function __construct(string $parameterName, mixed $parameterValue)
    {
        $this->parameterName = $parameterName;
        $this->parameterValue = $parameterValue;
    }

    public function execute(PipelineContext $context): PipelineContext
    {
        $context->setParameter($this->parameterName, $this->parameterValue);
        return $context;
    }
}