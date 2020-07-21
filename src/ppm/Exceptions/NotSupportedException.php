<?php


    namespace ppm\Exceptions;

    use LogicException;
    use Throwable;

    /**
     * Class NotSupportedException
     * @package ppm\Exceptions
     */
    class NotSupportedException extends LogicException
    {
        /**
         * NotSupportedException constructor.
         * @param string $message
         * @param int $code
         * @param Throwable|null $previous
         */
        public function __construct($message = "", $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }
    }