<?php

namespace Pipeline;

/**
 * Represents a context parameter's value
 */
class ContextParameterValue
{
    private array $value = array();
    public function __construct(mixed $value = null) {
        if (!is_null($value)) {
            $this->value[] = $value;
        }
    }

    public function add(mixed $value) {
        $this->value[] = $value;
    }

    public function resetTo(mixed $value) {
        $this->value = array($value);
    }

    public function reset() {
        $this->value = array();
    }

    public function getLast(): mixed
    {
        if (count($this->value) > 0) {
            return end($this->value);
        }
        else {
            return null;
        }
    }

    public function getAll(): array {
        return $this->value;
    }
}