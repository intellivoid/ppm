<?php


    namespace ppm\Exceptions;


    use RuntimeException;
    use Throwable;

    /**
     * Class IOException
     * @package ppm\Exceptions
     */
    class IOException extends RuntimeException
    {
        /**
         * IOException constructor.
         * @param string $message
         * @param int $code
         * @param Throwable|null $previous
         */
        public function __construct($message = "", $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }
    }
