<?php

    declare(strict_types=1);

    namespace ppm\Utilities;

    use ppm\Classes\Html;
    use Throwable;

    /**
     * Class Helpers
     * @package ppm\Utilities
     */
    class Helpers
    {
        /**
         * Captures PHP output into a string.
         *
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

        /**
         * Returns the last PHP error as plain string.
         *
         * @return string
         */
        public static function getLastError(): string
        {
            $message = error_get_last()['message'] ?? '';
            $message = ini_get('html_errors') ? Html::htmlToText($message) : $message;
            $message = preg_replace('#^\w+\(.*?\): #', '', $message);
            return $message;
        }

        /**
         * Converts false to null.
         *
         * @param $val
         * @return null
         */
        public static function falseToNull($val)
        {
            return $val === false ? null : $val;
        }

        /**
         * Finds the best suggestion (for 8-bit encoding).
         *
         * @param array $possibilities
         * @param string $value
         * @return string|null
         */
        public static function getSuggestion(array $possibilities, string $value): ?string
        {
            $best = null;
            $min = (strlen($value) / 4 + 1) * 10 + .1;
            foreach (array_unique($possibilities) as $item)
            {
                if ($item !== $value && ($len = levenshtein($item, $value, 10, 11, 10)) < $min)
                {
                    $min = $len;
                    $best = $item;
                }
            }
            return $best;
        }
    }