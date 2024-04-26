<?php

namespace Pipeline\Stages\SumOperation;
require_once PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractPipelineStage.php";
require_once PLUGIN_PATH . "classes/Pipeline/StageConfiguration/StageConfiguration.php";

use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\PipelineContext;
use Pipeline\StageConfiguration\StageConfiguration;
use Pipeline\StageDescriptor;

class SumOperationStage extends AbstractPipelineStage
{
    private StageConfiguration $stageConfiguration;

    public function __construct(StageConfiguration $stageConfiguration)
    {
        $this->stageConfiguration = $stageConfiguration;
    }

    public function execute(PipelineContext $context): PipelineContext
    {
        $operandA = $this->stageConfiguration->getSettingValue("operandA", $context, true);
        $operandB = $this->stageConfiguration->getSettingValue("operandB", $context, true);
        $resultParameter = $this->stageConfiguration->getSettingValue("resultTo", $context, false, "SUM_RESULT");
        $context->setParameter($resultParameter, $operandA + $operandB);
        return $context;
    }
}