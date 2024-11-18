<?php

namespace Pipeline\Stages\SetValue;
require_once WP_PIPEFLOW_PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractPipelineStage.php";

use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\PipelineContext;
use Pipeline\StageConfiguration\StageConfiguration;
use Pipeline\StageDescriptor;

class SetValueStage extends AbstractPipelineStage
{
    private StageConfiguration $stageConfiguration;

    public function __construct($stageConfiguration)
    {
        $this->stageConfiguration = $stageConfiguration;
    }

    public function execute(PipelineContext $context): PipelineContext
    {
        //Inputs
        $parameterName = $this->stageConfiguration->getSettingValue("parameterName", $context, true);
        $parameterValue = $this->stageConfiguration->getSettingValue("parameterValue", $context, true);

        //Output
        $context->setParameter($parameterName, $parameterValue);
        return $context;
    }
}