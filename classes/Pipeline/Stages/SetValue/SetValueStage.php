<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";

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
        if (is_array($parameterValue)) {
            print("Setting param value for $parameterName: count: " . count($parameterValue));
        }
        $context->setParameter($parameterName, $parameterValue);
        return $context;
    }
}