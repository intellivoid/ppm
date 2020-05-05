<?php


    namespace ppm;


    use ppm\Exceptions\InvalidPackageException;
    use ppm\Exceptions\PathNotFoundException;
    use ppm\Objects\Package;
    use ppm\Objects\Source;
    use ppm\Utilities\CLI;

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidComponentException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidDependencyException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidPackageException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'MissingPackagePropertyException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'PathNotFoundException.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Package' . DIRECTORY_SEPARATOR . 'Component.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Package' . DIRECTORY_SEPARATOR . 'Dependency.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Package' . DIRECTORY_SEPARATOR . 'Metadata.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Package.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Source.php');

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
        /**
         * @param string $path
         * @return Source
         * @throws Exceptions\InvalidComponentException
         * @throws Exceptions\MissingPackagePropertyException
         * @throws InvalidPackageException
         * @throws PathNotFoundException
         */
        public static function loadSource(string $path): Source
        {
            $SourceObject = new Source();
            $SourceObject->Path = $path;

            if(file_exists($path) == false)
            {
                throw new PathNotFoundException("The path '$path' was not found");
            }

            if(file_exists($SourceObject->getPackageConfigurationPath()) == false)
            {
                throw new InvalidPackageException("The file 'package.json' was not found, is this a valid ppm package?");
            }

            $PackageConfigurationContents = file_get_contents($SourceObject->getPackageConfigurationPath());
            $SourceObject->Package = Package::fromArray(
                json_decode($PackageConfigurationContents, true),
                $SourceObject->Path
            );

            return $SourceObject;
        }
    }

    if (PHP_SAPI === 'cli')
    {
        if(isset(CLI::options()["ppm"]))
        {
            CLI::start();
        }
    }