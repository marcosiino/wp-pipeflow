<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";

class RandomValueStage extends AbstractPipelineStage
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
        $minValue = $this->stageConfiguration->getSettingValue("minValue", $context, false, 0);
        $maxValue = $this->stageConfiguration->getSettingValue("maxValue", $context, false, getrandmax());

        //Output
        $context->setParameter($parameterName, rand($minValue, $maxValue));
        return $context;
    }
}