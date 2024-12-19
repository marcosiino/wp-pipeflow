<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Utils/Helpers.php";
class ArrayPathStage extends AbstractPipelineStage
{
    private StageConfiguration $stageConfiguration;

    public function __construct(StageConfiguration $stageConfiguration)
    {
        $this->stageConfiguration = $stageConfiguration;
    }

    /**
     * @inheritDoc
     */
    public function execute(PipelineContext $context): PipelineContext
    {
        $array = $this->stageConfiguration->getSettingValue("array", $context, true);
        $path = $this->stageConfiguration->getSettingValue("path", $context, true);
        $defaultValue = $this->stageConfiguration->getSettingValue("defaultPath", $context, false, null);
        $resultTo = $this->stageConfiguration->getSettingValue("resultTo", $context, true);

        $value = Helpers::getArrayItemAtPath($array, $path);
        if(is_null($value)) {
            if(!is_null($defaultValue)) {
                return $defaultValue;
            }
            else {
                throw new PipelineExecutionException("The specified path does not exist in the array: '$path'.");
            }
        }

        $context->setParameter($resultTo, $value);
        return $context;
    }
}