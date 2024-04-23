<?php

namespace Pipeline\Stages\TestStage;

require_once "classes/Pipeline/Interfaces/AbstractPipelineStage.php";

use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\PipelineContext;
use Pipeline\PromptProcessor;
use Pipeline\StageDescriptor;

class TestStage implements AbstractPipelineStage
{
    private string $prompt;

    static public function getDescriptor(): StageDescriptor
    {
        $stageDescription = "Just a test stage using for test purposes.";
        $inputs = array();
        $outputs = array(
            "PROCESSED_PROMPT" => "The processed prompt is output in this parameter after the stage is executed",
        );
        return new StageDescriptor("TestStage", $stageDescription, $inputs, $outputs);
    }

    public function __construct($prompt)
    {
        $this->prompt = $prompt;
    }

    public function execute(PipelineContext $context): PipelineContext
    {
        $promptProcessor = new PromptProcessor();
        $processedPrompt = $promptProcessor->process($this->prompt);
        $context["PROCESSED_PROMPT"] = $processedPrompt;
        return $context;
    }
}