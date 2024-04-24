<?php

namespace Pipeline\Utils\Parser;

enum ParsedElementSubType
{
    case plain;
    case indexed;
    case array;
}