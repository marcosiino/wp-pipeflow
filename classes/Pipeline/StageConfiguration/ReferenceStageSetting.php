<?php

namespace Pipeline\StageConfiguration;

use Pipeline\Exceptions\PipelineExecutionException;
use Pipeline\PipelineContext;

class ReferenceStageSetting
{
    private string $name;
    private string $referencedContextParam;
    private int|String|null $referencedKey;

    public function __construct(string $name, string $referencedContextParam, int|String|null $referencedKey = null) {
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
     * @throws PipelineExecutionException
     */
    public function getValue(PipelineContext $context): mixed {
        $contextParam = $context->getParameter($this->name);
        if(is_null($contextParam)) {
            return null; // Returns null if the context parameter doesn't exists
        }

        $contextParamValue = $contextParam->getAll();
        if (is_array($contextParamValue) && !is_null($this->referencedKey)) {
            if(array_key_exists($this->referencedKey, $contextParamValue)) {
                return $contextParamValue[$this->referencedContextParam];
            }
            else {
                throw new PipelineExecutionException("ReferenceStageSetting with name `$this->name` is referencing a key which doesn't exists on context parameter named: `$this->referencedContextParam`. Key: `$this->name` doesn't exists");
            }
        }
        else {
            return $contextParamValue;
        }
    }
}