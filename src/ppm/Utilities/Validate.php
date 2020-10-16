<?php


    namespace ppm\Utilities;

    /**
     * Class Validator
     * @package ppm\Utilities
     */
    class Validate
    {
        /**
         * Validates the user friendly package name
         *
         * @param $input
         * @return bool
         */
        public static function UserFriendlyPackageName($input): bool
        {
            if($input == null)
            {
                return false;
            }

            if(strlen($input) == 0)
            {
                return false;
            }

            if(strlen($input) > 64)
            {
                return false;
            }

            return true;
        }

        /**
         * Validates a organization name
         *
         * @param $input
         * @return bool
         */
        public static function Organization($input): bool
        {
            if($input == null)
            {
                return false;
            }

            if(strlen($input) == 0)
            {
                return false;
            }

            if(strlen($input) > 64)
            {
                return false;
            }

            return true;
        }

        /**
         * Validates an author
         *
         * @param $input
         * @return bool
         */
        public static function Author($input): bool
        {
            if($input == null)
            {
                return false;
            }

            if(strlen($input) == 0)
            {
                return false;
            }

            if(strlen($input) > 64)
            {
                return false;
            }

            return true;
        }


        /**
         * Validates a package description
         *
         * @param $input
         * @return bool
         */
        public static function Description($input): bool
        {
            if($input == null)
            {
                return false;
            }

            if(strlen($input) == 0)
            {
                return false;
            }

            if(strlen($input) > 1256)
            {
                return false;
            }

            return true;
        }

        /**
         * Validates if the package name is valid
         *
         * @param $input
         * @return bool
         */
        public static function PackageName($input): bool
        {
            if($input == null)
            {
                return false;
            }

            if((bool)preg_match("/^[a-z][a-z0-9_]*(\.[a-z0-9_]+)+[0-9a-z_]$/", $input) == false)
            {
                return false;
            }

            return true;
        }

        /**
         * Validates if the version number is valid
         *
         * @param $input
         * @return bool
         */
        public static function Version($input): bool
        {
            if($input == null)
            {
                return false;
            }

            // Added compatibility for composer package versions
            if((bool)preg_match("/^([0-9]+)\.([0-9]+)(?:-([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?(?:\+[0-9A-Za-z-]+)?$/", $input))
            {
                return true;
            }

            if((bool)preg_match("/^([0-9]+)\.([0-9]+)\.([0-9]+)(?:-([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?(?:\+[0-9A-Za-z-]+)?$/", $input))
            {
                return true;
            }

            if((bool)preg_match("/^([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)(?:-([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?(?:\+[0-9A-Za-z-]+)?$/", $input))
            {
                return true;
            }

            return false;
        }

        /**
         * Validates a URL
         *
         * @param $input
         * @return bool
         */
        public static function Url($input): bool
        {
            if($input == null)
            {
                return false;
            }

            if(filter_var($input, FILTER_VALIDATE_URL) == false)
            {
                return false;
            }

            return true;
        }
    }