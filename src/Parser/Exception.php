<?php

namespace Ivan\Parser;

class Exception extends \Exception
{
    const ERROR_EMPTY_FILE = 1;
    const ERROR_INVALID_STRUCTURE = 2;
    const ERROR_SEMANTIC_ERROR = 3;
}
