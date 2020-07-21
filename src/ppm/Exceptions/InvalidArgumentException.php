<?php /** @noinspection PhpUnused */


    namespace ppm\Exceptions;


    use Throwable;

    /**
     * Class InvalidArgumentException
     * @package ppm\Exceptions
     */
    class InvalidArgumentException extends \InvalidArgumentException
    {
        /**
         * InvalidArgumentException constructor.
         * @param string $message
         * @param int $code
         * @param Throwable|null $previous
         */
        public function __construct($message = "", $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }
    }