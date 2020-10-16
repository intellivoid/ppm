<?php


    namespace ppm;


    use ppm\Classes\AutoIndexer;
    use ppm\Exceptions\PackageNotFoundException;
    use ppm\Objects\PackageLock;
    use ppm\Utilities\CLI;
    use ppm\Utilities\PathFinder;
    use ppm\Utilities\System;
    use PpmZiProto\ZiProto;

    // Composer
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Service" . DIRECTORY_SEPARATOR . "IsThisEmpty.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Service" . DIRECTORY_SEPARATOR . "AbstractMap.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Service" . DIRECTORY_SEPARATOR . "AbstractClass.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Service" . DIRECTORY_SEPARATOR . "NamespaceMap.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Service" . DIRECTORY_SEPARATOR . "PackageMap.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Service" . DIRECTORY_SEPARATOR . "PropertyHelper.php");

    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Json" . DIRECTORY_SEPARATOR . "Archive.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Json" . DIRECTORY_SEPARATOR . "Author.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Json" . DIRECTORY_SEPARATOR . "Autoload.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Json" . DIRECTORY_SEPARATOR . "Config.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Json" . DIRECTORY_SEPARATOR . "Scripts.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Json" . DIRECTORY_SEPARATOR . "Support.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "ComposerJson.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Json" . DIRECTORY_SEPARATOR . "Repository.php");

    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Lockfile" . DIRECTORY_SEPARATOR . "Source.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Lockfile" . DIRECTORY_SEPARATOR . "Dist.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Lockfile" . DIRECTORY_SEPARATOR . "Source.php");

    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Lockfile.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Factory.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Wrapper.php");

    // Core
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Interfaces" . DIRECTORY_SEPARATOR . "HtmlString.php");

    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Abstracts" . DIRECTORY_SEPARATOR . "AutoloadMethod.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Abstracts" . DIRECTORY_SEPARATOR . "CompilerFlags.php");

    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoIndexer.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Callback.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "FileSystem.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Finder.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "GitManager.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Html.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Json.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "ObjectHelpers.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Strings.php");

    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "ApplicationException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "AutoloadBuilderException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "AutoloaderException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "CacheException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "ClassDependencySorterException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "CollectorException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "CollectorResultException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "ComposerIteratorException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "GithubPersonalAccessTokenAlreadyExistsException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "GithubPersonalAccessTokenNotFoundException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "InvalidArgumentException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "InvalidComponentException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "InvalidConfigurationException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "InvalidDependencyException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "InvalidPackageException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "InvalidPackageLockException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "InvalidStateException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "IOException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "JsonException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "MemberAccessException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "MissingPackagePropertyException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "NotSupportedException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "PackageNotFoundException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "ParserException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "PathNotFoundException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "RegexpException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "UnexpectedValueException.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "VersionNotFoundException.php");

    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Objects" . DIRECTORY_SEPARATOR . "GithubVault" . DIRECTORY_SEPARATOR . "PersonalAccessToken.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Objects" . DIRECTORY_SEPARATOR . "Package" . DIRECTORY_SEPARATOR . "Configuration" . DIRECTORY_SEPARATOR . "MainExecution.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Objects" . DIRECTORY_SEPARATOR . "Package" . DIRECTORY_SEPARATOR . "Component.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Objects" . DIRECTORY_SEPARATOR . "Package" . DIRECTORY_SEPARATOR . "Configuration.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Objects" . DIRECTORY_SEPARATOR . "Package" . DIRECTORY_SEPARATOR . "Dependency.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Objects" . DIRECTORY_SEPARATOR . "Package" . DIRECTORY_SEPARATOR . "Metadata.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Objects" . DIRECTORY_SEPARATOR . "PackageLock" . DIRECTORY_SEPARATOR . "PackageLockItem.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Objects" . DIRECTORY_SEPARATOR . "PackageLock" . DIRECTORY_SEPARATOR . "VersionConfiguration.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Objects" . DIRECTORY_SEPARATOR . "Sources" . DIRECTORY_SEPARATOR . "ComposerSource.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Objects" . DIRECTORY_SEPARATOR . "Sources" . DIRECTORY_SEPARATOR . "GithubSource.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Objects" . DIRECTORY_SEPARATOR . "GithubVault.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Objects" . DIRECTORY_SEPARATOR . "GitRepo.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Objects" . DIRECTORY_SEPARATOR . "Package.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Objects" . DIRECTORY_SEPARATOR . "PackageLock.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Objects" . DIRECTORY_SEPARATOR . "Source.php");

    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Traits" . DIRECTORY_SEPARATOR . "SmartObject.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Traits" . DIRECTORY_SEPARATOR . "StaticClass.php");

    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Utilities" . DIRECTORY_SEPARATOR . "CLI" . DIRECTORY_SEPARATOR . "Compiler.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Utilities" . DIRECTORY_SEPARATOR . "CLI" . DIRECTORY_SEPARATOR . "GithubVault.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Utilities" . DIRECTORY_SEPARATOR . "CLI" . DIRECTORY_SEPARATOR . "PackageManager.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Utilities" . DIRECTORY_SEPARATOR . "CLI" . DIRECTORY_SEPARATOR . "Runner.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Utilities" . DIRECTORY_SEPARATOR . "CLI" . DIRECTORY_SEPARATOR . "Tools.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Utilities" . DIRECTORY_SEPARATOR . "Autoloader.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Utilities" . DIRECTORY_SEPARATOR . "CLI.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Utilities" . DIRECTORY_SEPARATOR . "Compatibility.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Utilities" . DIRECTORY_SEPARATOR . "DateTime.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Utilities" . DIRECTORY_SEPARATOR . "Helpers.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Utilities" . DIRECTORY_SEPARATOR . "IO.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Utilities" . DIRECTORY_SEPARATOR . "PathFinder.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Utilities" . DIRECTORY_SEPARATOR . "System.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Utilities" . DIRECTORY_SEPARATOR . "Validate.php");

    // Directory Scanner
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "DirectoryScanner" . DIRECTORY_SEPARATOR . "Exception.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "DirectoryScanner" . DIRECTORY_SEPARATOR . "PHPFilterIterator.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "DirectoryScanner" . DIRECTORY_SEPARATOR . "FilesOnlyFilterIterator.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "DirectoryScanner" . DIRECTORY_SEPARATOR . "IncludeExcludeFilterIterator.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "DirectoryScanner" . DIRECTORY_SEPARATOR . "DirectoryScanner.php");

    // Autoloader Builder
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "ParserInterface.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "StaticListRenderer.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "Application.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "AutoloadRenderer.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "Cache.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "CacheEntry.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "CacheWarmingListRenderer.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "CachingParser.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "ClassDependencySorter.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "Collector.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "CollectorResult.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "ComposerIterator.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "Config.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "Factory.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "Parser.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "ParseResult.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "PathComparator.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "PharBuilder.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "SourceFile.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "StaticRenderer.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoloaderBuilder" . DIRECTORY_SEPARATOR . "StaticRequireListRenderer.php");

    // API
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "functions.php");

    // Dependencies
    if(class_exists("PpmParser\Parser") == false)
    {
        include_once(__DIR__ . DIRECTORY_SEPARATOR . "PpmParser" . DIRECTORY_SEPARATOR . "PpmParser.php");
    }

    if(class_exists("PpmZiProto\ZiProto") == false)
    {
        include_once(__DIR__ . DIRECTORY_SEPARATOR . "PpmZiProto" . DIRECTORY_SEPARATOR . "ZiProto.php");
    }

    // Define PPM definitions
    if(defined("PPM") == false)
    {
        $ppm_info_path = __DIR__ . DIRECTORY_SEPARATOR . "ppm.json";

        if(file_exists($ppm_info_path) == false)
        {
            trigger_error("The file '$ppm_info_path' does not exist" , E_USER_WARNING);
        }
        else
        {
            $ppm_info = json_decode(file_get_contents($ppm_info_path), true);
            define("PPM_VERSION", $ppm_info["VERSION"]);
            define("PPM_AUTHOR", $ppm_info["AUTHOR"]);
            define("PPM_URL", $ppm_info["URL"]);
            define("PPM_STATE", $ppm_info["STATE"]);
        }

        define("PPM", true);
        define("PPM_INSTALL", __DIR__);
        define("PPM_DATA", PathFinder::getMainPath(false));
    }

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
        if(isset(CLI::options()["ppm"]))
        {
            CLI::start();
        }
    }