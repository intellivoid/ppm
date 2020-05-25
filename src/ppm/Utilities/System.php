<?php


    namespace ppm\Utilities;

    /**
     * Class System
     * @package ppm\Utilities
     */
    class System
    {
        /**
         * @return bool
         */
        public static function isRoot(): bool
        {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            {
                return true;
            }

            if (posix_getuid() == 0)
            {
               return true;
            }

            return false;
        }

        /**
         * Sets permissions to the given path
         *
         * @param string $path
         * @param int $permissions
         * @return bool
         */
        public static function setPermissions(string $path, int $permissions): bool
        {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            {
                return false;
            }

            chmod($path, $permissions);
            return true;
        }
    }