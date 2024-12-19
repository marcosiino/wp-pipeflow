<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";

class ArrayCountStage extends AbstractPipelineStage
{
    private StageConfiguration $stageConfiguration;

    public function __construct($stageConfiguration)
    {
        $this->stageConfiguration = $stageConfiguration;
    }

    public function execute(PipelineContext $context): PipelineContext
    {
        //Inputs
        $arrayParameterName = $this->stageConfiguration->getSettingValue("arrayParameterName", $context, true);
        $resultTo = $this->stageConfiguration->getSettingValue("resultTo", $context, true);

        //Output
        $array = $context->getParameter($arrayParameterName);
        if(!is_array($array)) {
            throw new PipelineExecutionException("The context parameter specified in $arrayParameterName is not an array.");
        }

        $context->setParameter($resultTo, count($array));
        return $context;
    }
}