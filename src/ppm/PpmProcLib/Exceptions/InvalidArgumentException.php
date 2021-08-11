<?php

    /** @noinspection PhpPropertyOnlyWrittenInspection */

    namespace PpmProcLib\Exceptions;

    use PpmProcLib\Interfaces\ExceptionInterface;
    use Throwable;

    /**
     * Class InvalidArgumentException
     * @package ProcLib\Exceptions
     */
    class InvalidArgumentException extends \InvalidArgumentException implements ExceptionInterface
    {
        /**
         * @var Throwable|null
         */
        private ?Throwable $previous;

        /**
         * InvalidArgumentException constructor.
         * @param string $message
         * @param int $code
         * @param Throwable|null $previous
         * @noinspection PhpPureAttributeCanBeAddedInspection
         */
        public function __construct($message = "", $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
            $this->message = $message;
            $this->code = $code;
            $this->previous = $previous;
        }
    }
