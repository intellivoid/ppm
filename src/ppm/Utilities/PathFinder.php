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
                $path = realpath("/ppm");
            }
            else
            {
                $path = realpath("/etc/ppm");
            }

            if($create)
            {
                mkdir($path);
            }

            return $path;
        }

    }