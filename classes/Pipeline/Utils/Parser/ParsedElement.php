<?php

namespace Pipeline\Utils\Parser;
require_once PLUGIN_PATH . "classes/Pipeline/Utils/Parser/ParsedElementType.php";
require_once PLUGIN_PATH . "classes/Pipeline/Utils/Parser/ParsedElementSubType.php";

use Pipeline\Utils\Parser\ParsedElementType;
use Pipeline\Utils\Parser\ParsedElementSubType;

class ParsedElement {

    /**
     * The element name excluding enclosing characters etc... (for example "ELEMENT" if the matched element is "%{ELEMENT[1]}%")
     * @var string
     */
    public string $elementName;

    /**
     * The type of the parsed element
     *
     * @var \Pipeline\Utils\Parser\ParsedElementType
     */
    public ParsedElementType $elementType;

    /**
     * The subtype (i.e. if the element contains [value], its an indexed subtype)
     *
     * @var \Pipeline\Utils\Parser\ParsedElementType
     */
    public ParsedElementSubType $elementSubType;

    /**
     * The index, if subtype is an index type
     * @var int|null
     */
    public ?int $index;

    /**
     * The full string of the matched element (i.e. "%{ELEMENT[1]}%")
     * @var string
     */
    public string $fullElementMatch;

    public function __construct(string $elementName, ParsedElementType $elementType, ParsedElementSubType $elementSubType, int $index = null, string $fullElementMatch = '') {
        $this->elementName = $elementName;
        $this->elementType = $elementType;
        $this->elementSubType = $elementSubType;
        $this->index = $index;
        $this->fullElementMatch = $fullElementMatch;
    }
}
