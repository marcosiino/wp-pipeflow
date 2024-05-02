<?php

namespace Pipeline\Interfaces;
require_once PLUGIN_PATH . "classes/Pipeline/PipelineContext.php";
require_once PLUGIN_PATH . "classes/Pipeline/Exceptions/PipelineExecutionException.php";

use Pipeline\Exceptions\PipelineExecutionException;
use Pipeline\PipelineContext;

/**
 * Represents an abstract PipelineStage
 */
abstract class AbstractPipelineStage
{
    /**
     * Executes the pipeline stage with the context passed as argument, and returns the output context
     * @param PipelineContext $context
     * @return PipelineContext
     * @throws PipelineExecutionException
     */
    abstract public function execute(PipelineContext $context): PipelineContext;
}