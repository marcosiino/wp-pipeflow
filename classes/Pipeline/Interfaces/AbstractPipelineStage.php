<?php

namespace Pipeline\Interfaces;
require_once "classes/Pipeline/PipelineContext.php";
require_once "classes/Pipeline/StageDescriptor.php";

use Pipeline\PipelineContext;
use Pipeline\StageDescriptor;

/**
 * Represents an abstract PipelineStage
 */
interface AbstractPipelineStage
{
    /**
     * Executes the pipeline stage with the context passed as argument, and returns the output context
     * @param PipelineContext $context
     * @return PipelineContext
     */
    public function execute(PipelineContext $context): PipelineContext;
}