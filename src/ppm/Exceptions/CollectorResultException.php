<?php


    namespace ppm\Exceptions;


    use Exception;

    /**
     * Class CollectorResultException
     * @package ppm\Exceptions
     */
    class CollectorResultException extends Exception
    {
        const DuplicateUnitName = 1;
    }