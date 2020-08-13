<?php


    namespace ppm\Exceptions;


    use Exception;

    /**
     * Class CollectorException
     * @package ppm\Exceptions
     */
    class CollectorException extends Exception
    {
        const ParseError = 1;
        const RedeclarationFound = 2;
        const InFileRedeclarationFound = 3;
    }