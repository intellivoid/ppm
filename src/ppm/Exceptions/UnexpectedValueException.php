<?php /** @noinspection PhpUnused */


    namespace ppm\Exceptions;


    use Throwable;

    /**
     * Class UnexpectedValueException
     * @package ppm\Exceptions
     */
    class UnexpectedValueException extends \UnexpectedValueException
    {
        /**
         * UnexpectedValueException constructor.
         * @param string $message
         * @param int $code
         * @param Throwable|null $previous
         */
        public function __construct($message = "", $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }
    }