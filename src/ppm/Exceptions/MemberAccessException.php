<?php /** @noinspection PhpUnused */


    namespace ppm\Exceptions;


    use Error;
    use Throwable;

    /**
     * The exception that is thrown when accessing a class member (property or method) fails.
     */
    class MemberAccessException extends Error
    {
        public function __construct($message = "", $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }
    }