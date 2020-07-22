<?php


    namespace ppm\Exceptions;


    use Exception;
    use Throwable;

    /**
     * Class RegexpException
     * @package ppm\Exceptions
     */
    class RegexpException extends Exception
    {
        public const MESSAGES = [
            PREG_INTERNAL_ERROR => 'Internal error',
            PREG_BACKTRACK_LIMIT_ERROR => 'Backtrack limit was exhausted',
            PREG_RECURSION_LIMIT_ERROR => 'Recursion limit was exhausted',
            PREG_BAD_UTF8_ERROR => 'Malformed UTF-8 data',
            PREG_BAD_UTF8_OFFSET_ERROR => 'Offset didn\'t correspond to the begin of a valid UTF-8 code point',
            6 => 'Failed due to limited JIT stack space', // PREG_JIT_STACKLIMIT_ERROR
        ];

        /**
         * RegexpException constructor.
         * @param string $message
         * @param int $code
         * @param Throwable|null $previous
         */
        public function __construct($message = "", $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }
    }