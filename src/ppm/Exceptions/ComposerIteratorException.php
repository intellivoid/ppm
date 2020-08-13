<?php

    namespace ppm\Exceptions;

    use Exception;

    /**
     * Class ComposerIteratorException
     * @package ppm\Exceptions
     */
    class ComposerIteratorException extends Exception
    {
        const InvalidComposerJsonFile = 1;
    }

