<?php


    namespace ppm\Utilities\CLI;


    use Exception;
    use ppm\Classes\GitManager;
    use ppm\Exceptions\GithubPersonalAccessTokenNotFoundException;
    use ppm\Exceptions\InvalidPackageLockException;
    use ppm\Exceptions\VersionNotFoundException;
    use ppm\Objects\GithubVault;
    use ppm\Objects\Package;
    use ppm\Objects\PackageLock\PackageLockItem;
    use ppm\Objects\Sources\GithubSource;
    use ppm\ppm;
    use ppm\Utilities\CLI;
    use ppm\Utilities\IO;
    use ppm\Utilities\PathFinder;
    use ppm\Utilities\System;
    use PpmParser\JsonDecoder as JsonDecoderAlias;
    use PpmParser\Node\Scalar\MagicConst\File;
    use PpmParser\PrettyPrinter\Standard;
    use ZiProto\ZiProto;


    /**
     * Class Installer
     * @package ppm\Utilities\CLI
     */
    class PackageManager
    {
        /**
         * @param string $path
         * @throws InvalidPackageLockException
         */
        public static function installPackage(string $path)
        {
            // Install remotely from Github
            if(stripos($path, "github@") !== false)
            {
                try
                {
                    $github_source = GithubSource::parse($path);
                }
                catch (Exception $e)
                {
                    CLI::logError("Remote source parsing failed", $e);
                    exit(255);
                }

                if(isset(CLI::options()["branch"]))
                {
                    self::installGithubPackage($github_source, CLI::options()["branch"]);

                }
                else
                {
                    self::installGithubPackage($github_source);
                }

                exit(1);
            }

            if(file_exists($path) == false)
            {
                CLI::logError("The path '$path' does not exist");
                exit(255);
            }

            try
            {
                $PackageContents = ZiProto::decode(file_get_contents($path));
            }
            catch(Exception $e)
            {
                CLI::logError("The package cannot be opened correctly, the file may corrupted");
                exit(255);
            }

            if(isset($PackageContents['package']) == false)
            {
                CLI::logError("This package is missing information, is this a ppm package?");
                exit(255);
            }

            try
            {
                $PackageInformation = Package::fromArray($PackageContents['package']);
            }
            catch (Exception $e)
            {
                CLI::logError("There was an error while trying to read the package information", $e);
                exit(255);
            }

            if(System::isRoot() == false)
            {
                CLI::logError("This operation requires root privileges, please run ppm with 'sudo -H'");
                exit(255);
            }

            if(IO::writeTest(PathFinder::getMainPath(true)) == false)
            {
                CLI::logError("Write test failed, cannot write to the PPM installation directory");
                exit(255);
            }

            print("Installation Details" . PHP_EOL . PHP_EOL);
            print(" Package       :   \e[32m" . $PackageInformation->Metadata->PackageName . "\e[37m" . PHP_EOL);
            print(" Name          :   \e[32m" . $PackageInformation->Metadata->Name . "\e[37m" . PHP_EOL);
            print(" Version       :   \e[32m" . $PackageInformation->Metadata->Version . "\e[37m" . PHP_EOL);
            print(" Author        :   \e[32m" . $PackageInformation->Metadata->Author . "\e[37m" . PHP_EOL);
            print(" Organization  :   \e[32m" . $PackageInformation->Metadata->Organization . "\e[37m" . PHP_EOL);
            print(" URL           :   \e[32m" . $PackageInformation->Metadata->URL . PHP_EOL . "\e[37m" . PHP_EOL);
            print($PackageInformation->Metadata->Description . PHP_EOL . PHP_EOL);

            if(CLI::getBooleanInput("Do you want to install this package?") == false)
            {
                CLI::logError("Installation denied, aborting.");
                exit(255);
            }

            CLI::logEvent("Preparing installation");
            $PackageLock = ppm::getPackageLock();
            $package_name = $PackageInformation->Metadata->PackageName;
            $package_version =  $PackageInformation->Metadata->Version;

            if($PackageLock->packageExists($PackageInformation->Metadata->PackageName, $PackageInformation->Metadata->Version))
            {
                CLI::logError("Installation failed, the package " . $package_name . "==" . $package_version . " is already satisfied");
                exit(255);
            }

            // Check dependencies
            /** @var Package\Dependency $dependency */
            foreach($PackageInformation->Dependencies as $dependency)
            {
                if($PackageLock->packageExists($dependency->Package, $dependency->Version) == false)
                {
                    if($dependency->Required)
                    {
                        CLI::logError("Installation failed, This package requires the dependency '\e[37m" . $dependency->Package . "==\e[32m" .  $dependency->Version . "\e[91m' which is not installed");
                        exit(255);
                    }
                    else
                    {
                        CLI::logWarning("This package uses a non-required dependency '" . $dependency->Package . "==\e[32m" .  $dependency->Version . "\e[37m' which is not installed");
                    }
                }
            }

            CLI::logEvent("Installing " . $package_name . "==" . $package_version);
            $InstallationPath = PathFinder::getPackagePath(
                $PackageInformation->Metadata->PackageName, $PackageInformation->Metadata->Version, true
            );

            foreach($PackageContents["compiled_components"] as $component_name => $component)
            {
                if(stripos($component_name, "/"))
                {
                    $pieces = explode("/", $component_name);
                    $file_path = $InstallationPath . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pieces);
                    array_pop($pieces);
                    $path = $InstallationPath . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pieces);

                    if(file_exists($path) == false)
                    {
                        mkdir($path, 775, true);
                        System::setPermissions($path, 775);
                    }
                }
                else
                {
                    $file_path = $InstallationPath . DIRECTORY_SEPARATOR . $component_name;
                }

                $DecompiledComponent = json_encode(ZiProto::decode($component));
                $JsonDecoder = new JsonDecoderAlias();
                $prettyPrinter = new Standard;
                $AST = $JsonDecoder->decode($DecompiledComponent);
                file_put_contents($file_path, $prettyPrinter->prettyPrintFile($AST));
                System::setPermissions($file_path, 0744);
            }

            CLI::logEvent("Creating Package Data");

            $PackageDataPath = $InstallationPath . DIRECTORY_SEPARATOR . '.ppm';
            $PackageInformationPath = $PackageDataPath . DIRECTORY_SEPARATOR . 'PACKAGE';
            $PackageMainExecutionConfigPath = $PackageDataPath . DIRECTORY_SEPARATOR . 'MAIN_CONFIG';
            $PackageMainExecutionPath = $PackageDataPath . DIRECTORY_SEPARATOR . 'MAIN';
            $PackageAutoloaderPath = $PackageDataPath . DIRECTORY_SEPARATOR . 'COMPONENTS';

            mkdir($PackageDataPath);
            file_put_contents($PackageInformationPath, ZiProto::encode($PackageInformation->toArray()));
            file_put_contents($PackageAutoloaderPath, ZiProto::encode(array_keys($PackageContents["compiled_components"])));

            if($PackageContents["main_file"] !== null)
            {
                file_put_contents($PackageMainExecutionPath, $PackageContents["main_file"]);
            }

            if($PackageContents["main"] !== null)
            {
                file_put_contents($PackageMainExecutionConfigPath, ZiProto::encode($PackageContents["main"]));
            }

            CLI::logEvent("Updating Package Lock");
            $PackageLock->addPackage($PackageInformation);
            ppm::getAutoIndexer();
            ppm::savePackageLock($PackageLock);
        }

        /**
         * @param GithubSource $githubSource
         * @param string $branch
         * @throws InvalidPackageLockException
         * @noinspection PhpUnused
         */
        public static function installGithubPackage(GithubSource $githubSource, string $branch="master")
        {
            $github_vault = new GithubVault();
            $github_vault->load();

            try
            {
                $personal_access_token = $github_vault->get($githubSource->Alias);
            }
            catch (GithubPersonalAccessTokenNotFoundException $e)
            {
                CLI::logError("The alias is not registered in the Github vault, run 'ppm --github-add-pat'");
                exit(255);
            }

            if(System::isRoot() == false)
            {
                CLI::logError("This operation requires root privileges, please run ppm with 'sudo -H'");
                exit(255);
            }

            if(IO::writeTest(PathFinder::getMainPath(true)) == false)
            {
                CLI::logError("Write test failed, cannot write to the PPM installation directory");
                exit(255);
            }

            $clone_destination = PathFinder::getRemoteRepoPath(true) . DIRECTORY_SEPARATOR . $githubSource->toHash();

            if(file_exists($clone_destination))
            {
                IO::deleteDirectory($clone_destination);
            }

            try
            {
                CLI::logEvent("Cloning " . $githubSource->toName());
                mkdir($clone_destination);
                $repository = GitManager::clone_remote($clone_destination, $githubSource->toUri($personal_access_token));
            }
            catch (Exception $e)
            {
                CLI::logError("Clone failed", $e);
                exit(255);
            }

            try
            {
                CLI::logEvent("Checking out " . $branch);
                $repository->checkout($branch);
            }
            catch (Exception $e)
            {
                CLI::logError("Checkout failed", $e);
                exit(255);
            }

            $source_directory = Compiler::findSource($clone_destination);
            $compiled_file_path = Compiler::compilePackage($source_directory, PathFinder::getBuildPath(true), false);
            self::installPackage($compiled_file_path);
        }

        /**
         * @throws InvalidPackageLockException
         */
        public static function getInstalledPackages()
        {
            $PackageLock = ppm::getPackageLock();

            if(count($PackageLock->Packages) == 0)
            {
                CLI::logError("There are no installed PPM packages");
                exit(255);
            }

            /** @var PackageLockItem $packageLockItem */
            foreach($PackageLock->Packages as $packageLockItem)
            {
                foreach($packageLockItem->Versions as $version)
                {
                    print("\e[37m" . $packageLockItem->PackageName . "==\e[32m" . $version . "\e[37m" . PHP_EOL);
                }
            }

            exit(0);
        }

        /**
         * Uninstalls an existing package
         *
         * @param string $package
         * @param string $version
         * @throws InvalidPackageLockException
         * @throws VersionNotFoundException
         */
        public static function uninstallPackage(string $package, string $version="all")
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

            if(System::isRoot() == false)
            {
                CLI::logError("This operation requires root privileges, please run ppm with 'sudo -H'");
                exit(255);
            }

            if(IO::writeTest(PathFinder::getMainPath(true)) == false)
            {
                CLI::logError("Write test failed, cannot write to the PPM installation directory");
                exit(255);
            }

            if($version == "all")
            {
                if(CLI::getBooleanInput("You are about to uninstall all versions of $package, do you want to continue?") == false)
                {
                    CLI::logError("Installation denied, aborting.");
                    exit(255);
                }
            }
            elseif($version == "latest")
            {
                if(CLI::getBooleanInput("You are about to uninstall the latest version of $package, do you want to continue?") == false)
                {
                    CLI::logError("Installation denied, aborting.");
                    exit(255);
                }
            }
            else
            {
                if(CLI::getBooleanInput("You are about to uninstall $package==$version, do you want to continue?") == false)
                {
                    CLI::logError("Installation denied, aborting.");
                    exit(255);
                }
            }

            /** @var PackageLockItem $PackageLockItem */
            $PackageLockItem = $PackageLock->Packages[$package];

            if($version == "all")
            {
                foreach($PackageLock->Packages[$package]->Versions as $version)
                {
                    CLI::logEvent("Uninstalling " . $PackageLockItem->PackageName . "==" . $version);
                    IO::deleteDirectory($PackageLockItem->getPackagePath($version));
                    $PackageLock->removePackage($package, $version);
                }
            }
            else
            {
                if($version == "latest")
                {
                    $version = $PackageLockItem->getLatestVersion();
                }

                CLI::logEvent("Uninstalling " . $PackageLockItem->PackageName . "==" . $version);
                IO::deleteDirectory($PackageLockItem->getPackagePath($version));
                $PackageLock->removePackage($package, $version);
            }

            CLI::logEvent("Updating Package Lock");
            ppm::savePackageLock($PackageLock);
        }
    }