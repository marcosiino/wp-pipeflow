<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";

class JSONDecodeStage extends AbstractPipelineStage
{
    private StageConfiguration $stageConfiguration;

    public function __construct($stageConfiguration)
    {
        $this->stageConfiguration = $stageConfiguration;
    }

    public function execute(PipelineContext $context): PipelineContext
    {
        //Inputs
        $jsonString = $this->stageConfiguration->getSettingValue("jsonString", $context, true);
        $resultTo = $this->stageConfiguration->getSettingValue("resultTo", $context, true);

        //Output
        $resultArray = json_decode($jsonString, true);
        if(!is_array($resultArray)) {
            throw new PipelineExecutionException("JSON Decode failed");
        }

        $context->setParameter($resultTo, $resultArray);
        return $context;
    }
}