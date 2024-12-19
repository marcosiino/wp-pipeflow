<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";

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

        $value = $this->getArrayItemAtPath($array, $path);
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

    private function getArrayItemAtPath($array, $path)
    {
        //print_r($array);
        $keys = explode('.', $path);
        foreach ($keys as $key) {
            if (is_array($array) && array_key_exists($key, $array)) {
                //print("key $key: $array[$key]\n");
                $array = $array[$key];
            } else {
                //print("key $key: null\n");
                return null;
            }
        }
        return $array;
    }
}