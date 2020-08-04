<?php


    namespace ppm;


    use ppm\Classes\AutoIndexer;
    use ppm\Exceptions\PackageNotFoundException;
    use ppm\Objects\PackageLock;
    use ppm\Utilities\CLI;
    use ppm\Utilities\PathFinder;
    use ppm\Utilities\System;
    use ZiProto\ZiProto;

    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Interfaces" . DIRECTORY_SEPARATOR . "HtmlString.php");

    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Abstracts" . DIRECTORY_SEPARATOR . "AutoloadMethod.php");

    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "AutoIndexer.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Callback.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "FileSystem.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Finder.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "GitManager.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Html.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Json.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "ObjectHelpers.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Strings.php");

    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Exceptions" . DIRECTORY_SEPARATOR . "AutoloaderException.php");
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
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Utilities" . DIRECTORY_SEPARATOR . "DateTime.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Utilities" . DIRECTORY_SEPARATOR . "Helpers.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Utilities" . DIRECTORY_SEPARATOR . "IO.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Utilities" . DIRECTORY_SEPARATOR . "PathFinder.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Utilities" . DIRECTORY_SEPARATOR . "System.php");
    include_once(__DIR__ . DIRECTORY_SEPARATOR . "Utilities" . DIRECTORY_SEPARATOR . "Validate.php");

    include_once(__DIR__ . DIRECTORY_SEPARATOR . "functions.php");

    if(class_exists("PpmParser\Parser") == false)
    {
        include_once(__DIR__ . DIRECTORY_SEPARATOR . "PpmParser" . DIRECTORY_SEPARATOR . "PpmParser.php");
    }

    if(class_exists("ZiProto\ZiProto") == false)
    {
        include_once(__DIR__ . DIRECTORY_SEPARATOR . "ZiProto" . DIRECTORY_SEPARATOR . "ZiProto.php");
    }

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
         * @return PackageLock
         * @throws Exceptions\InvalidPackageLockException
         */
        public static function getPackageLock(): PackageLock
        {
            $path = PathFinder::getPackageLockPath(true);

            if(file_exists($path) == false)
            {
                $PackageLock = new PackageLock();
                file_put_contents($path, ZiProto::encode($PackageLock->toArray()));
                return $PackageLock;
            }

            return PackageLock::fromArray(ZiProto::decode(file_get_contents($path)));
        }

        /**
         * @param PackageLock $packageLock
         * @return bool
         */
        public static function savePackageLock(PackageLock $packageLock): bool
        {
            $path = PathFinder::getPackageLockPath(true);

            $contents = ZiProto::encode($packageLock->toArray());
            file_put_contents($path, $contents);
            System::setPermissions($path, 0744);

            return true;
        }

        /**
         * Imports a package, returns false if the package is already imported
         *
         * @param string $package
         * @param string $version
         * @return bool
         * @throws Exceptions\AutoloaderException
         * @throws Exceptions\InvalidComponentException
         * @throws Exceptions\InvalidPackageLockException
         * @throws Exceptions\VersionNotFoundException
         * @throws PackageNotFoundException
         */
        public static function import(string $package, string $version="latest"): bool
        {
            if(isset(self::$importedPackages[$package]))
            {
                trigger_error("The package $package==" . self::$importedPackages[$package] . " was already imported", E_USER_WARNING);
                return false;
            }

            $PackageLock = self::getPackageLock();

            if($PackageLock->packageExists($package, $version) == false)
            {
                throw new PackageNotFoundException("The package $package==$version is not installed");
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