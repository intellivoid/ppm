<?php

    namespace ppm\Exceptions;


    use Exception;

    /**
     * Class ParserException
     * @package ppm\Exceptions
     */
    class ParserException extends Exception
    {
        const ParseError = 1;
    }