<?php


    namespace ppm;


    use Exception;
    use ppm\Classes\AutoIndexer;
    use ppm\Exceptions\PackageNotFoundException;
    use ppm\Objects\PackageLock;
    use ppm\Utilities\CLI;
    use ppm\Utilities\PathFinder;
    use ppm\Utilities\System;
    use PpmZiProto\ZiProto;

    /**
     * Class ppm
     * @package ppm
     */
    class ppm
    {
        /**
         * @var array
         */
        private static $importedPackages;

        /**
         * @var AutoIndexer
         */
        private static $autoIndexer;

        /**
         * If enabled, PPM will throw warnings
         *
         * @var bool
         */
        public static $throwWarnings = false;

        /**
         * If enabled, the package lock will be stored in memory rather than loaded from
         * disk each time you import a package
         *
         * @var bool
         */
        public static $packageLockCacheEnabled = true;

        /**
         * The cache package lock
         *
         * @var PackageLock
         */
        private static $packageLockCache = null;

        /**
         * Loads the package lock from disk/cache
         *
         * @param bool $force_update
         * @return PackageLock
         * @throws Exceptions\InvalidPackageLockException
         */
        public static function getPackageLock(bool $force_update=false): PackageLock
        {
            CLI::logVerboseEvent("Getting package lock");
            if($force_update == false)
            {
                if(self::$packageLockCacheEnabled)
                {
                    if(self::$packageLockCache !== null)
                    {
                        CLI::logVerboseEvent("Package lock loaded from memory");
                        return self::$packageLockCache;
                    }
                }
            }

            $path = PathFinder::getPackageLockPath(true);
            CLI::logVerboseEvent("Package lock path: " . $path);

            if(file_exists($path) == false)
            {
                CLI::logVerboseEvent("Package lock doesn't exist, creating it");
                $PackageLock = new PackageLock();
                file_put_contents($path, ZiProto::encode($PackageLock->toArray()));
                return $PackageLock;
            }

            CLI::logVerboseEvent("Package lock saved in memory cache");
            self::$packageLockCache = PackageLock::fromArray(ZiProto::decode(file_get_contents($path)));
            return self::$packageLockCache;
        }

        /**
         * @param PackageLock $packageLock
         * @return bool
         * @throws Exceptions\InvalidPackageLockException
         */
        public static function savePackageLock(PackageLock $packageLock): bool
        {
            $path = PathFinder::getPackageLockPath(true);
            CLI::logVerboseEvent("Writing package lock to " . $path);

            $contents = ZiProto::encode($packageLock->toArray());
            file_put_contents($path, $contents);
            System::setPermissions($path, 0744);
            self::getPackageLock(true);

            return true;
        }

        /**
         * Imports a package, returns false if the package is already imported
         *
         * @param string $package
         * @param string $version
         * @param bool $import_dependencies
         * @param bool $throw_error
         * @return bool
         * @throws Exceptions\AutoloaderException
         * @throws Exceptions\InvalidComponentException
         * @throws Exceptions\InvalidPackageLockException
         * @throws Exceptions\VersionNotFoundException
         * @throws PackageNotFoundException
         */
        public static function import(string $package, string $version="latest", bool $import_dependencies=true, bool $throw_error=true): bool
        {
            if(isset(self::$importedPackages[$package]))
            {
                if(self::$throwWarnings)
                {
                    trigger_error("The package $package==" . self::$importedPackages[$package] . " was already imported", E_USER_WARNING);
                }
                return false;
            }

            $PackageLock = self::getPackageLock();

            if($PackageLock->packageExists($package, $version) == false)
            {
                if($throw_error)
                {
                    throw new PackageNotFoundException("The package $package==$version is not installed");
                }
            }

            // Import sub-dependencies
            if($import_dependencies)
            {
                if($version == "latest")
                {
                    $version = $PackageLock->getPackage($package)->getLatestVersion();
                }

                $version_configuration = $PackageLock->getPackage($package)->VersionConfigurations[$version];
                foreach($version_configuration->Dependencies as $dependency)
                {
                    if($dependency->Required)
                    {
                        self::import($dependency->Package, $dependency->Version, $import_dependencies, true);
                    }
                    else
                    {
                        try
                        {
                            self::import($dependency->Package, $dependency->Version, $import_dependencies, false);
                        }
                        catch(PackageNotFoundException $e)
                        {
                            unset($e);
                        }
                    }
                }
            }

            $PackageLock->getPackage($package)->import($version);
            self::$importedPackages[$package] = $version;

            return true;
        }

        /**
         * @return AutoIndexer
         */
        public static function getAutoIndexer(): AutoIndexer
        {
            if(ppm::$autoIndexer == null)
            {
                ppm::$autoIndexer = new AutoIndexer();
                ppm::$autoIndexer->setTempDirectory(PathFinder::getCachePath(true));
            }

            return self::$autoIndexer;
        }

        /**
         * Returns all the packages that were imported
         *
         * @return array
         * @noinspection PhpUnused
         */
        public static function getImportedPackages(): array
        {
            return self::$importedPackages;
        }
    }

    if (PHP_SAPI === "cli")
    {
        $options = "";
        $long_opts = array("ppm");
        $args = getopt("", $long_opts);

        if(isset($args["ppm"]))
        {
            // Execute the autoloader
            require_once(__DIR__ . DIRECTORY_SEPARATOR . "Autoloaders" . DIRECTORY_SEPARATOR . "main.php");
            ppm_load_full();

            // Start the CLI Program of PPM
            try
            {
                CLI::start();
            }
            catch(Exception $e)
            {
                CLI::logError("There was an unexpected error with PPM", $e);
                exit(128);
            }
        }
    }