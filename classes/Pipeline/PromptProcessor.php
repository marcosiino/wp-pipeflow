<?php

namespace Pipeline;
require_once "classes/Pipeline/PipelineContext.php";

class PromptProcessor
{
    private PipelineContext $context;

    public function __construct(PipelineContext $context) {
        $this->context = $context;
    }

    public function process(string $prompt): String
    {
        $placeholders = $this->extractPlaceholders($prompt);
        foreach($placeholders as $placeholder) {
            $placeholderName = $placeholder["placeholderName"];
            $value = $this->getValueForPlaceholder($placeholder);

            $prompt = str_replace($placeholderName, $value, $prompt);
        }

        return $prompt;
    }

    private function getValueForPlaceholder(array $placeholder): string {
        switch($placeholder["type"]) {
            case "variable":
                return (string)$this->context->getParameter($placeholder["parameterName"])->getLast();
            case "array":
                $array = $this->context->getParameter($placeholder["parameterName"])->getAll();
                $index = $placeholder["index"];
                if($index >= 0 && $index < count($array)) {
                    return (string)$array[$index];
                }
                else {
                    return "";
                }
        }
        return "";
    }

    private function extractPlaceholders(string $prompt): array {
        //Matches the placeholder of types %%NAME%% and %%NAME[index]%% where index is an integer >= 0 and extracts them in an array of $matches where each item is a match of a placeholder
        preg_match_all('/%%([a-zA-Z_]+)(\[\d+\])?%%/', $prompt, $matches, PREG_SET_ORDER);

        $placeholders = [];
        foreach ($matches as $match) {
            //The item 1 is the name of the placeholder (without %%), the item 2, if present, is the index within [].
            if (isset($match[2])) { //If an index has been specified in the placeholder
                //Add it to the $placeholder array with both the name and the index and type = "array"
                $placeholders[] = [
                    'parameterName' => $match[1],
                    'placeholderName' => "%%" . $match[1] . "%%",
                    'type' => 'array',
                    'index' => trim($match[2], '[]')
                ];
            } else { // If only the name of the placeholder is specified (without index) and type = "variable"
                //Add only the name to the placeholder index
                $placeholders[] = [
                    'parameterName' => $match[1],
                    'placeholderName' => "%%" . $match[1] . "%%",
                    'type' => 'variable',
                ];
            }
        }
        return $placeholders;
    }
}