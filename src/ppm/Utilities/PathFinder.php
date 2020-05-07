<?php


    namespace ppm\Utilities;

    /**
     * Class PathFinder
     * @package ppm\Utilities
     */
    class PathFinder
    {
        /**
         * @param bool $create
         * @return string
         */
        public static function getMainPath(bool $create=false): string
        {
            $path = null;
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            {
                $path = realpath(DIRECTORY_SEPARATOR);
            }
            else
            {
                $path = realpath(DIRECTORY_SEPARATOR . 'etc');
            }

            $path .= DIRECTORY_SEPARATOR . 'ppm';

            if($create)
            {
                if(file_exists($path) == false)
                {
                    mkdir($path);
                }
            }

            return $path;
        }

        /**
         * @param string $package
         * @param string $version
         * @param bool $create
         * @return string
         */
        public static function getPackagePath(string $package, string $version, bool $create=false): string
        {
            $directory_name = "packages" . DIRECTORY_SEPARATOR . $package . "==" . $version;
            $path = self::getMainPath($create) . DIRECTORY_SEPARATOR . $directory_name;

            if($create)
            {
                if(file_exists(self::getMainPath($create) . DIRECTORY_SEPARATOR . "packages") == false)
                {
                    mkdir(self::getMainPath($create) . DIRECTORY_SEPARATOR . "packages");
                }

                if(file_exists($path) == false)
                {
                    mkdir($path);
                }
            }

            return $path;
        }

        public static function getPackageLockPath(bool $create=false): string
        {
            return self::getMainPath($create) . DIRECTORY_SEPARATOR . "ppm.lock";
        }

    }