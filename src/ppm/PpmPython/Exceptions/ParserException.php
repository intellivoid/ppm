<?php


    namespace PpmPython\Exceptions;


    use Exception;
    use Throwable;

    /**
     * Class ParserException
     * @package PpmPython\Exceptions
     */
    class ParserException extends Exception
    {
        /**
         * @var Throwable|null
         */
        private $previous;

        /**
         * ParserException constructor.
         * @param string $message
         * @param int $code
         * @param Throwable|null $previous
         */
        public function __construct($message = "", $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
            $this->message = $message;
            $this->code = $code;
            $this->previous = $previous;
        }
    }