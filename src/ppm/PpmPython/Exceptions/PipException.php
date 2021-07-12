<?php


    namespace PpmPython\Exceptions;


    use Throwable;

    /**
     * Class PipException
     * @package PpmPython\Exceptions
     */
    class PipException extends \Exception
    {
        /**
         * @var Throwable|null
         */
        private $previous;

        /**
         * PipException constructor.
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