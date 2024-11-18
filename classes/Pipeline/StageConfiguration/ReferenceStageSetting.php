<?php

namespace Pipeline\StageConfiguration;
require_once WP_PIPEFLOW_PLUGIN_PATH . "classes/Pipeline/StageConfiguration/ReferenceStageSettingType.php";

use Pipeline\Exceptions\PipelineExecutionException;
use Pipeline\PipelineContext;

class ReferenceStageSetting
{
    private ReferenceStageSettingType $type;
    private string $name;
    private string $referencedContextParam;
    private int|String|null $referencedKey;

    public function __construct(ReferenceStageSettingType $type, string $name, string $referencedContextParam, int|String|null $referencedKey = null) {
        $this->type = $type;
        $this->name = $name;
        $this->referencedContextParam = $referencedContextParam;
        $this->referencedKey = $referencedKey;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getReferencedContextParam(): string {
        return $this->referencedContextParam;
    }

    /**
     * Returns the value of the referenced context parameter from the context passed as argument
     *
     * @return mixed|null
     * @throws PipelineExecutionException
     */
    public function getValue(PipelineContext $context): mixed {
        $contextParam = $context->getParameter($this->referencedContextParam);
        if(is_null($contextParam)) {
            return null; // Returns null if the context parameter doesn't exists
        }

        switch($this->type) {
            case ReferenceStageSettingType::plain:
                return $contextParam;
            case ReferenceStageSettingType::indexed:
                if(!is_array($contextParam)) {
                    throw new PipelineExecutionException("ReferenceStageSetting with name `$this->name` has an `indexed` reference to the context parameter named `$this->referencedContextParam` but it is not an array.");
                }

                if(is_null($this->referencedKey)) {
                    throw new PipelineExecutionException("ReferenceStageSetting with name `$this->name` has an `indexed` reference to the context parameter named `$this->referencedContextParam` but the referencedKey (index attribute of the param in the xml configuration) is null.");
                }

                if(array_key_exists($this->referencedKey, $contextParam)) {
                    return $contextParam[$this->referencedKey];
                }
                else {
                    throw new PipelineExecutionException("ReferenceStageSetting with name `$this->name` is referencing a key which doesn't exists on context parameter named: `$this->referencedContextParam`. Key: `$this->referencedKey` doesn't exists");
                }
            case ReferenceStageSettingType::last:
                if(!is_array($contextParam)) {
                    throw new PipelineExecutionException("ReferenceStageSetting with name `$this->name` has an `indexed` reference to the context parameter named `$this->referencedContextParam` but it is not an array.");
                }
                return end($contextParam);
        }

        return null;
    }
}