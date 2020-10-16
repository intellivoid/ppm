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

        /**
         * @param string $package
         * @param string $version
         * @param bool $create
         * @return string
         */
        public static function getPackageDataPath(string $package, string $version, bool $create=false): string
        {
            return self::getPackagePath($package, $version, $create) . DIRECTORY_SEPARATOR . '.ppm';
        }

        public static function getPackageLockPath(bool $create=false): string
        {
            return self::getMainPath($create) . DIRECTORY_SEPARATOR . "ppm.lock";
        }

        public static function getGithubVaultPath(bool $create=false): string
        {
            return self::getMainPath($create) . DIRECTORY_SEPARATOR . "github.vault";
        }

        public static function getRemoteRepoPath(bool $create=false): string
        {
            if($create)
            {
                if(file_exists(self::getMainPath($create) . DIRECTORY_SEPARATOR . "repos") == false)
                {
                    mkdir(self::getMainPath($create) . DIRECTORY_SEPARATOR . "repos");
                }
            }

            return self::getMainPath($create) . DIRECTORY_SEPARATOR . "repos";
        }

        public static function getComposerTemporaryPath(bool $create=false): string
        {
            if($create)
            {
                if(file_exists(self::getMainPath($create) . DIRECTORY_SEPARATOR . "composer") == false)
                {
                    mkdir(self::getMainPath($create) . DIRECTORY_SEPARATOR . "composer");
                }
            }

            return self::getMainPath($create) . DIRECTORY_SEPARATOR . "composer";
        }

        public static function getBuildPath(bool $create=false): string
        {
            if($create)
            {
                if(file_exists(self::getMainPath($create) . DIRECTORY_SEPARATOR . "builds") == false)
                {
                    mkdir(self::getMainPath($create) . DIRECTORY_SEPARATOR . "builds");
                    System::setPermissions(self::getMainPath($create) . DIRECTORY_SEPARATOR . "builds", 0777);
                }
            }

            return self::getMainPath($create) . DIRECTORY_SEPARATOR . "builds";
        }

        public static function getCachePath(bool $create=false): string
        {
            if($create)
            {
                if(file_exists(self::getMainPath($create) . DIRECTORY_SEPARATOR . "cache") == false)
                {
                    mkdir(self::getMainPath($create) . DIRECTORY_SEPARATOR . "cache");
                    System::setPermissions(self::getMainPath($create) . DIRECTORY_SEPARATOR . "cache", 0777);
                }
            }

            return self::getMainPath($create) . DIRECTORY_SEPARATOR . "cache";
        }

        public static function getLinksPath(bool $create=false): string
        {
            if($create)
            {
                if(file_exists(self::getMainPath($create) . DIRECTORY_SEPARATOR . "links") == false)
                {
                    mkdir(self::getMainPath($create) . DIRECTORY_SEPARATOR . "links");
                }
            }

            return self::getMainPath($create) . DIRECTORY_SEPARATOR . "links";
        }
    }