<?php

namespace Pipeline\Interfaces;
require_once PLUGIN_PATH . "classes/Pipeline/PipelineContext.php";
require_once PLUGIN_PATH . "classes/Pipeline/StageDescriptor.php";

use Pipeline\Exceptions\PipelineExecutionException;
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
     * @throws PipelineExecutionException
     */
    public function execute(PipelineContext $context): PipelineContext;
}