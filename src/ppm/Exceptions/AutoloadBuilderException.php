<?php


    namespace ppm\Exceptions;

    use Exception;

    /**
     * Class AutoloadBuilderException
     * @package ppm\Exceptions
     */
    class AutoloadBuilderException extends Exception
    {

        const TemplateNotFound = 1;
        const InvalidTimestamp = 2;

    }