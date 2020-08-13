<?php


    namespace ppm\Exceptions;


    use Exception;

    /**
     * Class ClassDependencySorterException
     * @package ppm\Exceptions
     */
    class ClassDependencySorterException extends Exception
    {
        const TooManyDependencyLevels = 1;

    }