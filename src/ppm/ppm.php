<?php


    namespace ppm;


    use ppm\Utilities\CLI;

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidComponentException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidPackageException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'MissingPackagePropertyException.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Package' . DIRECTORY_SEPARATOR . 'Component.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Package' . DIRECTORY_SEPARATOR . 'Metadata.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Package.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Utilities' . DIRECTORY_SEPARATOR . 'CLI.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Utilities' . DIRECTORY_SEPARATOR . 'PathFinder.php');

    define("PPM_VERSION", "1.0.0.0");
    define("PPM_AUTHOR", "Zi Xing Narrakas");

    /**
     * Class ppm
     * @package ppm
     */
    class ppm
    {

    }

    if (PHP_SAPI === 'cli')
    {
        if(isset(CLI::cli_options()["ppm"]))
        {
            CLI::cli_process();
        }
    }