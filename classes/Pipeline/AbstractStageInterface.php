<?php

namespace Pipeline;

/**
 * Represents an abstract PipelineStage
 */
interface AbstractPipelineStage
{
    /**
     * Returns the description of this pipeline stage
     * @return StageDescriptor
     */
    static public function getDescriptor(): StageDescriptor;

    /**
     * Executes the pipeline stage with the context passed as argument, and returns the output context
     * @param PipelineContext $context
     * @return PipelineContext
     */
    public function execute(PipelineContext $context): PipelineContext;
}