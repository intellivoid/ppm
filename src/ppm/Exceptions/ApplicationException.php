<?php


    namespace ppm\Exceptions;


    use Exception;

    /**
     * Class ApplicationException
     * @package ppm\Exceptions
     */
    class ApplicationException extends Exception
    {
        const NoUnitsFound = 1;
        const OpenSSLError = 2;
    }