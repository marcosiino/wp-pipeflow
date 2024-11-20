<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/PipelineContext.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Utils/Parser/InputParser.php";

/**
 * Process a string by replacing the placeholders with the context parameters values
 */

class PlaceholderProcessor
{
    private PipelineContext $context;

    public function __construct(PipelineContext $context) {
        $this->context = $context;
    }

    public function process(string $prompt): String
    {
        $placeholders = InputParser::extractElements($prompt);
        foreach($placeholders as $placeholder) {
            if($placeholder->elementType == ParsedElementType::placeholder) {
                $value = $this->getValueForPlaceholder($placeholder);
                $prompt = str_replace($placeholder->fullElementMatch, $value, $prompt);
            }
        }

        return $prompt;
    }

    private function getValueForPlaceholder(ParsedElement $placeholder): string {
        switch($placeholder->elementSubType) {
            case ParsedElementSubType::plain:
                return (string)$this->context->getParameter($placeholder->elementName);
            case ParsedElementSubType::indexed:
                $param = $this->context->getParameter($placeholder->elementName);
                if(!is_array($param)) {
                    return "";
                }
                $index = $placeholder->index;
                if($index >= 0 && $index < count($param)) {
                    return (string)$param[$index];
                }
                else {
                    return "";
                }
        }
        return "";
    }
}