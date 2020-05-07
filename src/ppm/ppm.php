<?php


    namespace ppm;


    use ppm\Objects\PackageLock;
    use ppm\Utilities\CLI;
    use ppm\Utilities\PathFinder;

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'AutoloadMethod.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'AutoloaderException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidComponentException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidConfigurationException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidDependencyException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidPackageException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidPackageLockException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'MissingPackagePropertyException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'PathNotFoundException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'VersionNotFoundException.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Package' . DIRECTORY_SEPARATOR . 'Component.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Package' . DIRECTORY_SEPARATOR . 'Configuration.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Package' . DIRECTORY_SEPARATOR . 'Dependency.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Package' . DIRECTORY_SEPARATOR . 'Metadata.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'PackageLock' . DIRECTORY_SEPARATOR . 'PackageLockItem.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'PackageLock' . DIRECTORY_SEPARATOR . 'VersionConfiguration.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Package.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'PackageLock.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Source.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Utilities' . DIRECTORY_SEPARATOR . 'Autoloader.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Utilities' . DIRECTORY_SEPARATOR . 'CLI.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Utilities' . DIRECTORY_SEPARATOR . 'PathFinder.php');

    if(class_exists("PpmParser\Parser") == false)
    {
        include_once(__DIR__ . DIRECTORY_SEPARATOR . 'PpmParser' . DIRECTORY_SEPARATOR . 'PpmParser.php');
    }

    if(class_exists("ZiProto\ZiProto") == false)
    {
        include_once(__DIR__ . DIRECTORY_SEPARATOR . 'ZiProto' . DIRECTORY_SEPARATOR . 'ZiProto.php');
    }

    define("PPM_VERSION", "1.0.0.0");
    define("PPM_AUTHOR", "Zi Xing Narrakas");

    /**
     * Class ppm
     * @package ppm
     */
    class ppm
    {
        public static function getPackageLock(): PackageLock
        {
            $path = PathFinder::getPackageLockPath(true);

            if(file_exists($path) == false)
            {
                $PackageLock = new PackageLock();
                file_put_contents($path, json_encode($PackageLock->toArray()));
                return $PackageLock;
            }

            return PackageLock::fromArray(json_decode(file_get_contents($path), true));
        }

        public static function savePackageLock(PackageLock $packageLock): bool
        {
            $path = PathFinder::getPackageLockPath(true);

            $contents = json_encode($packageLock->toArray(), JSON_PRETTY_PRINT);
            file_put_contents($path, $contents);

            return true;
        }
    }

    if (PHP_SAPI === 'cli')
    {
        if(isset(CLI::options()["ppm"]))
        {
            CLI::start();
        }
    }