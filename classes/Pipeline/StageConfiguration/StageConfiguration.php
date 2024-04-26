<?php

namespace Pipeline\StageConfiguration;

use Pipeline\Exceptions\PipelineExecutionException;
use Pipeline\PipelineContext;

class StageConfiguration
{
    private array $configuration = array();

    public function addSetting(StageSetting|ReferenceStageSetting $setting): void
    {
        $this->configuration[$setting->getName()] = $setting;
    }

    /**
     * @throws PipelineExecutionException
     */
    public function getSettingValue(string $name, PipelineContext $context, bool $required = false, mixed $defaultValue = null) {
        if(!array_key_exists($name, $this->configuration)) {
            if($required === true) {
                throw new PipelineExecutionException("Stage configuration setting `$name` is required");
            }
            else {
                return $defaultValue;
            }
        }

        $setting = $this->configuration[$name];
        if($setting instanceof StageSetting) {
            return $setting->getValue();
        }
        else if($setting instanceof ReferenceStageSetting) {
            $value = $setting->getValue($context);
            if(is_null($value)) {
                if($required === true) {
                    throw new PipelineExecutionException("Stage configuration setting `$name` is required, but the referenced context param named '" . $setting->getReferencedContextParam() . "' does not exist");
                }
                else {
                    return $defaultValue;
                }
            }
            else {
                return $value;
            }
        }

        return $defaultValue;
    }
}