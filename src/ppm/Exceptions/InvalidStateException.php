<?php


    namespace ppm\Exceptions;

    use Throwable;

    /**
     * The exception that is thrown when a method call is invalid for the object's
     * current state, method has been invoked at an illegal or inappropriate time.
     *
     * Class InvalidStateException
     * @package ppm\Exceptions
     */
    class InvalidStateException extends \RuntimeException
    {
        /**
         * InvalidStateException constructor.
         * @param string $message
         * @param int $code
         * @param Throwable|null $previous
         */
        public function __construct($message = "", $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }
    }
