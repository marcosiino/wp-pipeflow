<?php

namespace Pipeline\Utils\Parser;
require_once PLUGIN_PATH . "classes/Pipeline/Utils/Parser/ParsedElementType.php";
require_once PLUGIN_PATH . "classes/Pipeline/Utils/Parser/ParsedElementSubType.php";
require_once PLUGIN_PATH . "classes/Pipeline/Utils/Parser/ParsedElement.php";

/**
 * Parses the pipeline parameters inputs
 */
class InputParser
{
    /**
     * @param string $inputString
     * @return array array of extracted ParsedElement
     */
    public static function extractElements(string $inputString): array {
        $elements = [];

        // Placeholders
        preg_match_all('/(%%([^%[\]]+)%%)/', $inputString, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $elements[] = new ParsedElement($match[2], ParsedElementType::placeholder, ParsedElementSubType::plain, null, $match[1]);
        }

        // Indexed Placeholders
        preg_match_all('/(%%([^%[\]]+)\[(\d+)\]%%)/', $inputString, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $elements[] = new ParsedElement($match[2], ParsedElementType::placeholder, ParsedElementSubType::indexed, $match[3], $match[1]);
        }

        // References
        preg_match_all('/(%{([^}%\[\]]+)}%)/', $inputString, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $elements[] = new ParsedElement($match[2], ParsedElementType::reference, ParsedElementSubType::plain, null, $match[1]);
        }

        // Indexed References
        preg_match_all('/(%{([^}%]+)\[(\d+)\]}%)/', $inputString, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $elements[] = new ParsedElement($match[2], ParsedElementType::reference, ParsedElementSubType::indexed, $match[3], $match[1]);
        }

        // References Arrays
        preg_match_all('/(%{\[([^}%]+)\]}%)/', $inputString, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $elements[] = new ParsedElement($match[2], ParsedElementType::reference, ParsedElementSubType::array, null, $match[1]);
        }

        return $elements;
    }

}