<?php


    namespace ppm\Utilities\CLI;

    use ppm\Exceptions\InvalidPackageLockException;
    use ppm\Exceptions\VersionNotFoundException;
    use ppm\Objects\PackageLock\PackageLockItem;
    use ppm\Objects\PackageLock\VersionConfiguration;
    use ppm\ppm;
    use ppm\Utilities\CLI;

    /**
     * Class Runner
     * @package ppm\Utilities\CLI
     */
    class Runner
    {
        /**
         * Executes the main executable of the package
         *
         * @param string $package
         * @param string $version
         * @param string $arguments
         * @throws InvalidPackageLockException
         * @throws VersionNotFoundException
         */
        public static function executePackageMain(string $package, string $version="latest", string $arguments=null)
        {
            $PackageLock = ppm::getPackageLock();

            if($PackageLock->packageExists($package, $version) == false)
            {
                if($version == "all" || $version == "latest")
                {
                    CLI::logError("The package $package is not installed");
                    exit(255);
                }
                else
                {
                    CLI::logError("The package $package==$version is not installed");
                    exit(255);
                }
            }

            /** @var PackageLockItem $PackageLockItem */
            $PackageLockItem = $PackageLock->Packages[$package];


            if($version == "all")
            {
                CLI::logError("The package version 'all' is not applicable to execution");
                exit(255);
            }
            else
            {
                if($version == "latest")
                {
                    $version = $PackageLockItem->getLatestVersion();
                }
            }

            /** @var VersionConfiguration $VersionConfiguration */
            $VersionConfiguration = $PackageLockItem->VersionConfigurations[$version];

            if($VersionConfiguration->Main == null)
            {
                CLI::logError("The package $package==$version is not executable");
                exit(255);
            }

            $PPM_HolderPath = $PackageLockItem->getPackagePath($version) . DIRECTORY_SEPARATOR . ".ppm";
            $ExecutionPath = $PPM_HolderPath . DIRECTORY_SEPARATOR . "MAIN";

            if(file_exists($ExecutionPath) == false)
            {
                CLI::logError("The main executable '" . $ExecutionPath . "' does not exist");
                exit(255);
            }

            $exit_code = 0;
            chdir($PackageLockItem->getPackagePath($version));

            if($arguments == null)
            {
                passthru("php " . escapeshellarg($ExecutionPath),  $exit_code);
            }
            else
            {
                passthru("php " . escapeshellarg($ExecutionPath) . " " . escapeshellcmd($arguments),  $exit_code);
            }

            exit($exit_code);
        }
    }