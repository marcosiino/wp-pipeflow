<?php

namespace Pipeline\Interfaces;
require_once PLUGIN_PATH . "classes/Pipeline/PipelineContext.php";
require_once PLUGIN_PATH . "classes/Pipeline/StageDescriptor.php";
require_once PLUGIN_PATH . "classes/Pipeline/Exceptions/PipelineExecutionException.php";
require_once PLUGIN_PATH . "classes/Pipeline/Utils/Parser/InputParser.php";
require_once PLUGIN_PATH . "classes/Pipeline/Utils/Parser/ParsedElementType.php";
require_once PLUGIN_PATH . "classes/Pipeline/Utils/Parser/ParsedElementSubType.php";

use Pipeline\Exceptions\PipelineExecutionException;
use Pipeline\PipelineContext;
use Pipeline\StageDescriptor;
use Pipeline\Utils\Parser\InputParser;
use Pipeline\Utils\Parser\ParsedElementSubType;
use Pipeline\Utils\Parser\ParsedElementType;

/**
 * Represents an abstract PipelineStage
 */
abstract class AbstractPipelineStage
{
    /**
     * Executes the pipeline stage with the context passed as argument, and returns the output context
     * @param PipelineContext $context
     * @return PipelineContext
     * @throws PipelineExecutionException
     */
    abstract public function execute(PipelineContext $context): PipelineContext;

    /**
     * If the provided parameter is a reference to a context parameter (like %{PARAM}%) or %{PARAM[1]}% or %{[PARAM]}%, this functions retrieve and gets the value/values of the context parameter to which it points,
     * otherwise the value of the $inputParameter itself is returned. Please note that an array is returned in all cases, even if the values is a single one.
     * @param string $inputParameter - The input parameter
     * @param PipelineContext $context - The current context
     * @return mixed|array|null mixed value or array of mixed values or null if reference is not found or is invalid
     * @throws PipelineExecutionException
     */
    public function getInputValue(string $inputParameter, PipelineContext $context, bool $required = false): mixed {
        $elements = InputParser::extractElements($inputParameter);
        if (count($elements) === 0) {
            //The $parameter doesn't contain any reference, so its content its returned as is.
            return $inputParameter;
        }

        if (!$elements[0]->elementType == ParsedElementType::reference) {
            return $inputParameter;
        }

        $reference = $elements[0];
        switch($reference->elementSubType) {
            case ParsedElementSubType::plain:
                $contextParameter = $context->getParameter($reference->elementName);
                if(is_null($contextParameter)) {
                    if($required) {
                        throw new PipelineExecutionException("Invalid input parameter reference: $inputParameter. The input parameter is required but the referenced context parameter is not found.");
                    }
                    else {
                        return null;
                    }
                }
                return $contextParameter->getLast();
            case ParsedElementSubType::indexed:
                $contextParameter = $context->getParameter($reference->elementName);
                if(is_null($contextParameter)) {
                    if($required) {
                        throw new PipelineExecutionException("Invalid input parameter reference: $inputParameter. The input parameter is required but the referenced context parameter is not found.");
                    }
                    else {
                        return null;
                    }
                }
                $array = $contextParameter->getAll();
                if(array_key_exists($reference->index, $array)) {
                    return $array[$reference->index];
                }
                else {
                    if($required) {
                        throw new PipelineExecutionException("Invalid input parameter reference: $inputParameter. The input parameter is required but the referenced context parameter's index is out of bounds.");
                    }
                    else {
                        return null;
                    }
                }
            case ParsedElementSubType::array:
                $contextParameter = $context->getParameter($reference->elementName);
                if(is_null($contextParameter)) {
                    if($required) {
                        throw new PipelineExecutionException("Invalid input parameter reference: $inputParameter. The input parameter is required but the referenced context parameter is not found.");
                    }
                    else {
                        return null;
                    }
                }
                return $contextParameter->getAll();
        }
        if($required) {
            throw new PipelineExecutionException("Invalid input parameter: $inputParameter.");
        }
        else {
            return null;
        }
    }
}