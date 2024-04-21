<?php

namespace Pipeline;
use InvalidArgumentException;

/**
 * Represents the description of a PipelineStage, with its identifier, inputs and outputs descriptions
 */
class StageDescriptor
{
    private string $identifier;
    private array $inputs;
    private array $outputs;

    /**
     * Constructor for StageDescriptor.
     * @param string $identifier An identifier for the stage.
     * @param array $inputs An associative array of string => string to be used as inputs.
     * @param array $outputs An associative array of string => string to be used as outputs.
     */
    public function __construct(string $identifier, array $inputs, array $outputs) {
        $this->identifier = $identifier;
        $this->setInputs($inputs);
        $this->setOutputs($outputs);
    }

    /**
     * The stage identifier
     * @return string
     */
    public function getIdentifier(): string {
        return $this->identifier;
    }

    /**
     * Returns the stage inputs
     * @return array associative array of string (param name) => string (description)
     */
    public function getInputs(): array {
        return $this->inputs;
    }

    /**
     * Returns the outputs of the stage
     * @return array associative array of string (param name) => string (description)
     */
    public function getOutputs(): array {
        return $this->outputs;
    }

    private function validateDictionary(array $dictionary): bool {
        foreach ($dictionary as $key => $value) {
            if (!is_string($key) || !is_string($value)) {
                return false;
            }
        }
        return true;
    }

    private function setInputs(array $inputs): void {
        if ($this->validateDictionary($inputs)) {
            $this->inputs = $inputs;
        } else {
            throw new InvalidArgumentException("Inputs must be an associative array of string => string.");
        }
    }

    private function setOutputs(array $outputs): void {
        if ($this->validateDictionary($outputs)) {
            $this->outputs = $outputs;
        } else {
            throw new InvalidArgumentException("Outputs must be an associative array of string => string.");
        }
    }
}