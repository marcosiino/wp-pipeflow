<?php

namespace Pipeline\Interfaces;
require_once "classes/Pipeline/StageDescriptor.php";

use Pipeline\StageDescriptor;

/**
 * Represents an abstract StageFactory
 */
interface AbstractStageFactory
{
    /**
     * Instantiates a pipeline stage using the provided configuration
     *
     * @param array $configuration - an associative array used to instantiate and setup a stage, with at least an type field which identifies the stage type
     * @return AbstractPipelineStage
     */
    public function instantiate(array $configuration): AbstractPipelineStage;

    /**
     * Returns the pipeline stage's description for the type of PipelineStages that a concrete StageFactory instantiates
     *
     * @return StageDescriptor
     */
    public function getStageDescriptor(): StageDescriptor;
}