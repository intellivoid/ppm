<?php

    declare(strict_types=1);

    namespace ppm\Utilities;

    use Throwable;

    /**
     * Class Helpers
     * @package ppm\Utilities
     */
    class Helpers
    {
        /**
         * @param callable $func
         * @return string
         * @throws Throwable
         */
        public static function capture(callable $func): string
        {
            ob_start(function () {});
            try
            {
                $func();
                return ob_get_clean();
            }
            catch (Throwable $e)
            {
                ob_end_clean();
                throw $e;
            }
        }
    }