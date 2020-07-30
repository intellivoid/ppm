<?php


    namespace ppm\Exceptions;


    use Exception;
    use Throwable;

    /**
     * Class GithubPersonalAccessTokenNotFoundException
     * @package ppm\Exceptions
     */
    class GithubPersonalAccessTokenNotFoundException extends Exception
    {
        /**
         * GithubPersonalAccessTokenNotFoundException constructor.
         * @param string $message
         * @param int $code
         * @param Throwable|null $previous
         */
        public function __construct($message = "", $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }
    }