<?php


    namespace ppm\Exceptions;


    use Exception;
    use Throwable;

    /**
     * Class GithubPersonalAccessTokenAlreadyExistsException
     * @package ppm\Exceptions
     */
    class GithubPersonalAccessTokenAlreadyExistsException extends Exception
    {
        /**
         * GithubPersonalAccessTokenAlreadyExistsException constructor.
         * @param string $message
         * @param int $code
         * @param Throwable|null $previous
         */
        public function __construct($message = "", $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }
    }