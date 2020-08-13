<?php


    namespace ppm\Utilities\CLI;


    use Exception;
    use ppm\Abstracts\AutoloadMethod;
    use ppm\Classes\GitManager;
    use ppm\Exceptions\ApplicationException;
    use ppm\Exceptions\CollectorException;
    use ppm\Exceptions\GithubPersonalAccessTokenNotFoundException;
    use ppm\Exceptions\InvalidPackageLockException;
    use ppm\Exceptions\VersionNotFoundException;
    use ppm\Objects\GithubVault;
    use ppm\Objects\Package;
    use ppm\Objects\PackageLock\PackageLockItem;
    use ppm\Objects\PackageLock\VersionConfiguration;
    use ppm\Objects\Sources\GithubSource;
    use ppm\ppm;
    use ppm\Utilities\Autoloader;
    use ppm\Utilities\CLI;
    use ppm\Utilities\IO;
    use ppm\Utilities\PathFinder;
    use ppm\Utilities\System;
    use PpmParser\JsonDecoder as JsonDecoderAlias;
    use PpmParser\PrettyPrinter\Standard;
    use PpmZiProto\ZiProto;


    /**
     * Class Installer
     * @package ppm\Utilities\CLI
     */
    class PackageManager
    {
        private static function optionIsSet(array $options, string $option): bool
        {
            if(isset($options[$option]))
            {
                return true;
            }

            return false;
        }

        /**
         * Installs a package onto the system
         *
         * @param string $path
         * @param array $options
         * @throws InvalidPackageLockException
         */
        public static function installPackage(string $path, array $options=array())
        {
            // Install remotely from Github
            if(stripos($path, "@github") !== false)
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

            if(self::optionIsSet($options, "no_details") == false)
            {
                print("Installation Details" . PHP_EOL . PHP_EOL);
                print(" Package       :   \e[32m" . $PackageInformation->Metadata->PackageName . "\e[37m" . PHP_EOL);
                print(" Name          :   \e[32m" . $PackageInformation->Metadata->Name . "\e[37m" . PHP_EOL);
                print(" Version       :   \e[32m" . $PackageInformation->Metadata->Version . "\e[37m" . PHP_EOL);

                if($PackageInformation->Metadata->Author !== null)
                {
                    print(" Author        :   \e[32m" . $PackageInformation->Metadata->Author . "\e[37m" . PHP_EOL);
                }

                if($PackageInformation->Metadata->Organization !== null)
                {
                    print(" Organization  :   \e[32m" . $PackageInformation->Metadata->Organization . "\e[37m" . PHP_EOL);
                }

                if($PackageInformation->Metadata->URL !== null)
                {
                    print(" URL           :   \e[32m" . $PackageInformation->Metadata->URL . PHP_EOL . "\e[37m" . PHP_EOL);
                }

                if($PackageInformation->Metadata->Description !== null)
                {
                    print($PackageInformation->Metadata->Description . PHP_EOL . PHP_EOL);
                }
            }

            if(self::optionIsSet($options, "no_prompt") == false)
            {
                if(CLI::getBooleanInput("Do you want to install this package?") == false)
                {
                    CLI::logError("Installation denied, aborting.");
                    exit(255);
                }
            }

            CLI::logEvent("Preparing installation");
            $PackageLock = ppm::getPackageLock();
            $package_name = $PackageInformation->Metadata->PackageName;
            $package_version =  $PackageInformation->Metadata->Version;

            if($PackageLock->packageExists($PackageInformation->Metadata->PackageName, $PackageInformation->Metadata->Version))
            {
                if(isset(CLI::options()["fix-conflict"]) || self::optionIsSet($options, "fix_conflict"))
                {
                    try
                    {
                        CLI::logEvent("Fixing conflicted package " . $package_name . "==" . $package_version);
                        self::uninstallPackage($package_name, $package_version, false);
                    }
                    catch (InvalidPackageLockException $e)
                    {
                        CLI::logError("Invalid package lock error", $e);
                        exit(255);
                    }
                    catch (VersionNotFoundException $e)
                    {
                        CLI::logError("Unexpected error, probably a bug. The package manager reports that the version of this package wasn't found.", $e);
                        exit(255);
                    }
                }
                else
                {
                    CLI::logError("Installation failed, the package " . $package_name . "==" . $package_version . " is already satisfied");
                    exit(255);
                }
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

            if(isset($PackageContents["pre_install"]))
            {
                foreach($PackageContents["pre_install"] as $script_id => $script)
                {
                    $first_line = trim(preg_replace('/\s\s+/', ' ', strtok($script, "\n")));
                    if(strtolower($first_line) == "<?php")
                    {
                        $script = substr($script, strpos($script, "\n"));
                    }

                    try
                    {
                        CLI::logEvent("Executing pre-install script '" . $script_id . "'");
                        eval($script);
                    }
                    catch(Exception $e)
                    {
                        CLI::logError("Cannot execute pre-install script '" . $script_id . "'", $e);
                    }
                }
            }

            foreach($PackageContents["compiled_components"] as $component_name => $component)
            {
                /** @noinspection DuplicatedCode */
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

            if(isset($PackageContents["byte_compiled"]))
            {
                foreach($PackageContents["byte_compiled"] as $component_name => $component)
                {
                    /** @noinspection DuplicatedCode */
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

                    $prettyPrinter = new Standard;
                    $AST = unserialize($component);
                    file_put_contents($file_path, $prettyPrinter->prettyPrintFile($AST));
                    System::setPermissions($file_path, 0744);
                }
            }

            if(isset($PackageContents["files"]))
            {
                if($PackageContents["files"] !== null)
                {
                    foreach($PackageContents["files"] as $file_name => $file)
                    {
                        /** @noinspection DuplicatedCode */
                        if(stripos($file_name, "/"))
                        {
                            $pieces = explode("/", $file_name);
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
                            $file_path = $InstallationPath . DIRECTORY_SEPARATOR . $file_name;
                        }

                        file_put_contents($file_path, $file);
                        System::setPermissions($file_path, 0744);
                    }
                }
            }

            $PackageDataPath = $InstallationPath . DIRECTORY_SEPARATOR . '.ppm';
            $PackageInformationPath = $PackageDataPath . DIRECTORY_SEPARATOR . 'PACKAGE';
            $PackageMainExecutionConfigPath = $PackageDataPath . DIRECTORY_SEPARATOR . 'MAIN_CONFIG';
            $PackageMainExecutionPath = $PackageDataPath . DIRECTORY_SEPARATOR . 'MAIN';
            $PackageAutoloaderPath = $PackageDataPath . DIRECTORY_SEPARATOR . 'COMPONENTS';

            mkdir($PackageDataPath);
            file_put_contents($PackageInformationPath, ZiProto::encode($PackageInformation->toArray()));
            file_put_contents($PackageAutoloaderPath, ZiProto::encode(array_keys($PackageContents["compiled_components"])));

            switch($PackageInformation->Configuration->AutoLoadMethod)
            {
                case AutoloadMethod::GeneratedStatic:
                    CLI::logEvent("Generating static autoloader process started");

                    try
                    {
                        Autoloader::generateStaticAutoLoader($InstallationPath);
                    }
                    catch (\ppm\Classes\DirectoryScanner\Exception $e)
                    {
                        CLI::logError("Directory scanner exception raised", $e);
                        exit(255);
                    }
                    catch (ApplicationException $e)
                    {
                        CLI::logError("Application exception raised", $e);
                        exit(255);
                    }
                    catch (CollectorException $e)
                    {
                        CLI::logError("Collector exception raised", $e);
                        exit(255);
                    }
                    break;

                case AutoloadMethod::StandardPhpLibrary:
                    CLI::logEvent("Generating standard autoloader process started");

                    try
                    {
                        Autoloader::generateStandardAutoLoader($InstallationPath);
                    }
                    catch (\ppm\Classes\DirectoryScanner\Exception $e)
                    {
                        CLI::logError("Directory scanner exception raised", $e);
                        exit(255);
                    }
                    catch (ApplicationException $e)
                    {
                        CLI::logError("Application exception raised", $e);
                        exit(255);
                    }
                    catch (CollectorException $e)
                    {
                        CLI::logError("Collector exception raised", $e);
                        exit(255);
                    }
                    break;

                default:
                    break;
            }

            if($PackageContents["main_file"] !== null)
            {
                file_put_contents($PackageMainExecutionPath, $PackageContents["main_file"]);
            }

            if($PackageContents["main"] !== null)
            {
                file_put_contents($PackageMainExecutionConfigPath, ZiProto::encode($PackageContents["main"]));

                $MainExecutionConfiguration = Package\Configuration\MainExecution::fromArray($PackageContents["main"]);
                if($MainExecutionConfiguration->CreateSymlink)
                {
                    CLI::logEvent("Creating symbolic link (latest only)");
                    $LinksPath = PathFinder::getLinksPath(true);
                    $ShellScript = "ppm --main=\"" . $PackageInformation->Metadata->PackageName . "\" --version=\"latest\" args=\"$@\"";
                    $ShellPath = $LinksPath . DIRECTORY_SEPARATOR . $PackageInformation->Metadata->PackageName;
                    $SystemExecutionPoint = realpath(DIRECTORY_SEPARATOR . "usr");
                    $SystemExecutionPoint .= DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . $MainExecutionConfiguration->Name;

                    if(file_exists($SystemExecutionPoint))
                    {
                        if(file_exists($ShellPath) == false)
                        {
                            CLI::logError("The symbolic link cannot be registered onto the system because the name '" . $MainExecutionConfiguration->Name . "' is already used by another program");
                            exit(255);
                        }
                    }

                    if(file_exists($SystemExecutionPoint))
                    {
                        unlink($SystemExecutionPoint);
                    }

                    file_put_contents($ShellPath, $ShellScript);
                    symlink($ShellPath, $SystemExecutionPoint);
                    System::setPermissions($ShellPath, 0755);
                    System::setPermissions($SystemExecutionPoint, 0755);
                }
            }

            if(self::optionIsSet($options, "update_source") == true)
            {
                $UpdateSourcePath = $PackageDataPath . DIRECTORY_SEPARATOR . 'UPDATE_SOURCE';
                file_put_contents($UpdateSourcePath, $options["update_source"]);
            }

            if(self::optionIsSet($options, "update_branch") == true)
            {
                $UpdateBranchPath = $PackageDataPath . DIRECTORY_SEPARATOR . 'UPDATE_BRANCH';
                file_put_contents($UpdateBranchPath, $options["update_branch"]);
            }

            if(isset($PackageContents["post_install"]))
            {
                foreach($PackageContents["post_install"] as $script_id => $script)
                {
                    $first_line = trim(preg_replace('/\s\s+/', ' ', strtok($script, "\n")));
                    if(strtolower($first_line) == "<?php")
                    {
                        $script = substr($script, strpos($script, "\n"));
                    }

                    try
                    {
                        CLI::logEvent("Executing post-install script '" . $script_id . "'");
                        eval($script);
                    }
                    catch(Exception $e)
                    {
                        CLI::logError("Cannot execute post-install script '" . $script_id . "'", $e);
                    }
                }
            }

            CLI::logEvent("Updating Package Lock");

            $PackageLock->addPackage($PackageInformation);
            ppm::savePackageLock($PackageLock);
            ppm::getAutoIndexer();
        }

        /**
         * @param GithubSource $githubSource
         * @param string $branch
         * @param array $options
         * @throws InvalidPackageLockException
         * @noinspection PhpUnused
         */
        public static function installGithubPackage(GithubSource $githubSource, string $branch="master", array $options=array())
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
            $options = array_merge($options, [
                "update_source" => (string)$githubSource,
                "update_branch" => $branch
            ]);

            self::installPackage($compiled_file_path, $options);

            CLI::logEvent("Cleaning up");
            IO::deleteDirectory($source_directory);
            unlink($compiled_file_path);
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
        }

        /**
         * Updates all installed packages from remote sources
         */
        public static function updateAllPackages()
        {
            try
            {
                $PackageLock = ppm::getPackageLock();
            }
            catch (InvalidPackageLockException $e)
            {
                CLI::logError("Package lock error", $e);
                exit(255);
            }

            if(count($PackageLock->Packages) == 0)
            {
                CLI::logError("There are no installed PPM packages");
                exit(255);
            }

            /** @var PackageLockItem $packageLockItem */
            foreach($PackageLock->Packages as $packageLockItem)
            {
                try
                {
                    self::updatePackage($packageLockItem->PackageName, false);
                }
                catch (InvalidPackageLockException $e)
                {
                    CLI::logError("Package lock error", $e);
                    exit(255);
                }
                catch (VersionNotFoundException $e)
                {
                    CLI::logError("Unexpected error, probably a bug. The package manager reports that a version of the package " . $packageLockItem->PackageName . " wasn't found.", $e);
                    exit(255);
                }
            }
        }

        /**
         * @param string $package
         * @param bool $hard_failure
         * @throws InvalidPackageLockException
         * @throws VersionNotFoundException
         */
        public static function updatePackage(string $package, bool $hard_failure=true)
        {
            $PackageLock = ppm::getPackageLock();

            if($PackageLock->packageExists($package, "latest") == false)
            {
                CLI::logError("The package $package is not installed");
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

            $UpdateSourcePath = null;
            $UpdateBranchPath = null;

            // Find the update source path
            $PackageLockItem = $PackageLock->Packages[$package];
            foreach($PackageLock->Packages[$package]->Versions as $version)
            {
                $ppm_path = $PackageLockItem->getPackagePath($version) . DIRECTORY_SEPARATOR . '.ppm';

                if(file_exists($ppm_path . DIRECTORY_SEPARATOR . "UPDATE_SOURCE"))
                {
                    $UpdateSourcePath = $ppm_path . DIRECTORY_SEPARATOR . "UPDATE_SOURCE";
                }

                if(file_exists($ppm_path . DIRECTORY_SEPARATOR . "UPDATE_BRANCH"))
                {
                    $UpdateBranchPath = $ppm_path . DIRECTORY_SEPARATOR . "UPDATE_BRANCH";
                }

                if($UpdateSourcePath !== null)
                {
                    break;
                }
            }

            if($UpdateSourcePath == null)
            {
                if($hard_failure)
                {
                    CLI::logError("The package '$package' cannot be updated (No remote source)");
                    exit(255);
                }
                else
                {
                    CLI::logWarning("The package '$package' cannot be updated (No remote source)");
                    return;
                }
            }

            if($UpdateBranchPath == null)
            {
                CLI::logWarning("The update source of '$package' contains no branch, assuming master branch");
                $UpdateBranch = "master";
            }
            else
            {
                $UpdateBranch = file_get_contents($UpdateBranchPath);
            }

            $UpdateSource = file_get_contents($UpdateSourcePath);

            if(stripos($UpdateSource, "@github") !== false)
            {
                try
                {
                    $github_source = GithubSource::parse($UpdateSource);
                }
                catch (Exception $e)
                {
                    CLI::logError("Remote source parsing failed (Assumed github)", $e);
                    exit(255);
                }

                if($UpdateBranch !== null)
                {
                    self::installGithubPackage($github_source, $UpdateBranch, [
                        "fix_conflict" => true,
                        "no_prompt" => true,
                        "no_details" => true
                    ]);
                }
                else
                {
                    self::installGithubPackage($github_source, "master", [
                        "fix_conflict" => true,
                        "no_prompt" => true,
                        "no_details" => true
                    ]);
                }
            }
        }

        /**
         * Uninstalls an existing package
         *
         * @param string $package
         * @param string $version
         * @param bool $prompt
         * @throws InvalidPackageLockException
         * @throws VersionNotFoundException
         */
        public static function uninstallPackage(string $package, string $version="all", bool $prompt=true)
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

            if($prompt)
            {
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
            }

            /** @var PackageLockItem $PackageLockItem */
            $PackageLockItem = $PackageLock->Packages[$package];
            $ExecutionMethodNames = array();

            /** @var VersionConfiguration $versionConfiguration */
            foreach($PackageLockItem->VersionConfigurations as $versionConfiguration)
            {
                if($versionConfiguration->Main !== null)
                {
                    if(in_array($versionConfiguration->Main->Name, $ExecutionMethodNames) == false)
                    {
                        $ExecutionMethodNames[] = $versionConfiguration->Main->Name;
                    }
                }
            }

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

            if($PackageLock->packageExists($package) == false)
            {
                $MainExecutionPath = PathFinder::getLinksPath(true) . DIRECTORY_SEPARATOR . $PackageLockItem->PackageName;
                $SystemExecutionPoint = realpath(DIRECTORY_SEPARATOR . "usr");

                if(file_exists($MainExecutionPath))
                {
                    CLI::logEvent("Removing symbolic link");
                    unlink($MainExecutionPath);
                }

                foreach($ExecutionMethodNames as $executionMethodName)
                {
                    $file_path = $SystemExecutionPoint . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . $executionMethodName;
                    if(is_link($file_path))
                    {
                        unlink($file_path);
                    }
                }
            }

            CLI::logEvent("Updating Package Lock");
            ppm::savePackageLock($PackageLock);
        }
    }