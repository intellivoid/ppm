<?php


    namespace ppm\Utilities;

    use Exception;
    use ppm\Exceptions\GithubPersonalAccessTokenAlreadyExistsException;
    use ppm\Exceptions\GithubPersonalAccessTokenNotFoundException;
    use ppm\Exceptions\InvalidPackageLockException;
    use ppm\Exceptions\VersionNotFoundException;
    use ppm\Objects\GithubVault;
    use ppm\Objects\Package;
    use ppm\Objects\PackageLock\PackageLockItem;
    use ppm\Objects\Source;
    use ppm\ppm;
    use PpmParser\JsonDecoder as JsonDecoderAlias;
    use PpmParser\PrettyPrinter\Standard;
    use ZiProto\ZiProto;

    /**
     * Class CLI
     * @package ppm\Utilities
     */
    class CLI
    {
        /**
         * Returns CLI options
         */
        public static function options()
        {
            $options = "";
            $long_opts = array(
                "ppm",
                "no-prompt",
                "no-intro",
                "github-add-pat",
                "github-remove-pat",
                "installed",
                "compile::",
                "alias::",
                "token::",
                "install::",
                "uninstall::",
                "version::"
            );

            return getopt($options, $long_opts);
        }

        public static function getParameter(string $name, string $text, bool $require_parameter): string
        {
            if(isset(CLI::options()[$name]))
            {
                return CLI::options()[$name];
            }
            else
            {
                if($require_parameter)
                {
                    self::logError("The required parameter '$name' is missing");
                    exit(255);
                }
            }

            return self::getInput($text . ": ");
        }

        public static function getBooleanInput(string $message): bool
        {
            if(isset(CLI::options()["no-prompt"]))
            {
                return true;
            }

            print($message . " [Y/n] ");

            $handle = fopen ("php://stdin","r");
            $line = fgets($handle);

            if(trim(strtolower($line)) != 'y')
            {
                return false;
            }

            return true;
        }

        public static function getInput(string $message): string
        {
            print($message);
            $handle = fopen ("php://stdin","r");
            return trim(preg_replace('/\s\s+/', ' ', fgets($handle)));
        }

        /**
         * Prints out the main CLI Intro
         */
        public static function displayIntro()
        {
            if(isset(CLI::options()["no-intro"]))
            {
                return;
            }

            print("\e[34m ________  ________  _____ ______      " . PHP_EOL);
            print("\e[34m|\   __  \|\   __  \|\   _ \  _   \    " . PHP_EOL);
            print("\e[34m\ \  \|\  \ \  \|\  \ \  \\\__\ \   \   " . PHP_EOL);
            print("\e[34m \ \   ____\ \   ____\ \  \\|__| \   \  " . PHP_EOL);
            print("\e[34m  \ \  \___|\ \  \___|\ \  \    \ \  \ " . PHP_EOL);
            print("\e[34m   \ \__\    \ \__\    \ \__\    \ \__\\" . PHP_EOL);
            print("\e[34m    \|__|     \|__|     \|__|     \|__|" . PHP_EOL);
            print(PHP_EOL);
            print("\033[37mVersion: \033[32m" . PPM_VERSION . "\033[37m | Written By " . PPM_AUTHOR . PHP_EOL);
            print("\033[37m==============================================" . PHP_EOL);
            print(PHP_EOL);
        }

        /**
         * Displays the help menu
         */
        public static function displayHelpMenu()
        {
            print("\033[37m \033[33m--compile\033[37m=\"<path>\"" . PHP_EOL);
            print("\033[37m     Compiles a PHP library from source to a .ppm file" . PHP_EOL);
            print("\033[37m \033[33m--install\033[37m=\"<path>\"" . PHP_EOL);
            print("\033[37m     Installs a .ppm package to the system" . PHP_EOL);
            print("\033[37m \033[33m--install\033[37m=\"github@<alias>/<organization>/<repo>\" \e[33m--branch\e[37m=\"<optional_branch>\"" . PHP_EOL);
            print("\033[37m     Compiles and installs from a GitHub repo" . PHP_EOL);
            print("\033[37m \033[33m--uninstall\033[37m=\"<package_name>\"" . PHP_EOL);
            print("\033[37m     Completely uninstalls a installed package from the system" . PHP_EOL);
            print("\033[37m \033[33m--uninstall\033[37m=\"<package_name>\" \e[33m--version\e[37m=\"<version>\"" . PHP_EOL);
            print("\033[37m     Uninstalls a specific version of the package from the system" . PHP_EOL);
            print("\033[37m \033[33m--installed" . PHP_EOL);
            print("\033[37m     Lists all the installed packages on the system" . PHP_EOL . PHP_EOL);

            print("\033[37m \033[33m--github-add-pat \e[33m--alias\e[37m=\"<alias>\" \e[33m--token\e[37m=\"<personal_access_token>\"" . PHP_EOL);
            print("\033[37m     Adds a GitHub personal access key to be used with the GitHub API (Secured)" . PHP_EOL);
            print("\033[37m \033[33m--github-remove-pat \e[33m--alias\e[37m=\"<alias>\"" . PHP_EOL);
            print("\033[37m     Removes a GitHub personal access key" . PHP_EOL);
        }

        /**
         * Logs an event
         *
         * @param string $message
         * @param bool $newline
         */
        public static function logEvent(string $message, bool $newline=true)
        {
            if($newline)
            {
                print("\033[33m > \e[37m $message" . PHP_EOL);
            }
            else
            {
                print("\033[33m > \e[37m $message");
            }
        }

        /**
         * @param string $message
         * @param Exception $exception
         */
        public static function logError(string $message, Exception $exception=null)
        {
            print("\e[91m " . $message . "\e[37m" . PHP_EOL);
            if(is_null($exception) == false)
            {
                print("\e[91m " . $exception->getMessage() . "\e[37m" . PHP_EOL);
            }
        }

        /**
         * @param string $message
         */
        public static function logWarning(string $message)
        {
            print("\e[33m WARNING: \e[37m " . $message . "\e[37m" . PHP_EOL);

        }

        /**
         * Processes the command-line options
         */
        public static function start()
        {
            self::displayIntro();

            if(isset(self::options()['compile']))
            {
                self::compilePackage(self::options()['compile']);
                return;
            }


            if(isset(self::options()['install']))
            {
                try
                {
                    self::installPackage(self::options()['install']);
                }
                catch (InvalidPackageLockException $e)
                {
                    self::logError("There was an error while installing the package", $e);
                    exit(255);
                }

                return;
            }

            if(isset(self::options()['uninstall']))
            {
                try
                {
                    if(isset(self::options()["version"]))
                    {

                        self::uninstallPackage(self::options()['uninstall'], self::options()['version']);

                    }
                    else
                    {
                        self::uninstallPackage(self::options()['uninstall']);
                    }
                }
                catch (InvalidPackageLockException $e)
                {
                    self::logError("Failed to uninstall package, Invalid Package Lock Error", $e);
                    exit(255);
                }
                catch (VersionNotFoundException $e)
                {
                    self::logError("Failed to uninstall package, Version not found", $e);
                    exit(255);
                }

                return;
            }

            if(isset(self::options()['installed']))
            {
                try
                {
                    self::getInstalledPackages();
                }
                catch (InvalidPackageLockException $e)
                {
                    self::logError("Failed to list installed packages, Invalid Package Lock Error", $e);
                    exit(255);
                }

                return;
            }

            if(isset(self::options()["github-add-pat"]))
            {
                self::githubAddPersonalAccessKey();
                return;
            }

            if(isset(self::options()["github-remove-pat"]))
            {
                self::githubRemovePersonalAccessKey();
                return;
            }

            self::displayHelpMenu();
        }

        /**
         * Removes a personal access key from the Github vault
         */
        public static function githubRemovePersonalAccessKey()
        {
            if(System::isRoot() == false)
            {
                self::logError("This operation requires root privileges, please run ppm with 'sudo -H'");
                exit(255);
            }

            if(IO::writeTest(PathFinder::getMainPath(true)) == false)
            {
                self::logError("Write test failed, cannot write to the PPM installation directory");
                exit(255);
            }

            $github_vault = new GithubVault();
            $github_vault->load();

            try
            {
                $personal_access_token = $github_vault->get(self::getParameter("alias", "Alias", false));
                $github_vault->delete($personal_access_token);
            }
            catch (GithubPersonalAccessTokenNotFoundException $e)
            {
                self::logError("Alias not registered in vault, aborting.");
                exit(255);
            }

            $github_vault->save();
            print("Personal Access Token removed." . PHP_EOL);
        }

        /**
         * Adds a personal access token to the Github vault
         */
        public static function githubAddPersonalAccessKey()
        {
            if(System::isRoot() == false)
            {
                self::logError("This operation requires root privileges, please run ppm with 'sudo -H'");
                exit(255);
            }

            if(IO::writeTest(PathFinder::getMainPath(true)) == false)
            {
                self::logError("Write test failed, cannot write to the PPM installation directory");
                exit(255);
            }

            $github_vault = new GithubVault();
            $github_vault->load();

            $alias = self::getParameter("alias", "Alias", false);
            $personal_access_token = self::getParameter("token", "Personal Access Token", false);

            try
            {
                $github_vault->add($alias, $personal_access_token);
            }
            catch (GithubPersonalAccessTokenAlreadyExistsException $e)
            {
                self::logError("Personal Access Token already defined in the Github vault, aborting.");
                exit(255);
            }

            $github_vault->save();
            print("Personal Access Token added." . PHP_EOL);
        }

        /**
         * @throws InvalidPackageLockException
         */
        public static function getInstalledPackages()
        {
            $PackageLock = ppm::getPackageLock();

            if(count($PackageLock->Packages) == 0)
            {
                self::logError("There are no installed PPM packages");
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
                    self::logError("The package $package is not installed");
                    exit(255);
                }
                else
                {
                    self::logError("The package $package==$version is not installed");
                    exit(255);
                }
            }

            if(System::isRoot() == false)
            {
                self::logError("This operation requires root privileges, please run ppm with 'sudo -H'");
                exit(255);
            }

            if(IO::writeTest(PathFinder::getMainPath(true)) == false)
            {
                self::logError("Write test failed, cannot write to the PPM installation directory");
                exit(255);
            }

            if($version == "all")
            {
                if(self::getBooleanInput("You are about to uninstall all versions of $package, do you want to continue?") == false)
                {
                    self::logError("Installation denied, aborting.");
                    exit(255);
                }
            }
            elseif($version == "latest")
            {
                if(self::getBooleanInput("You are about to uninstall the latest version of $package, do you want to continue?") == false)
                {
                    self::logError("Installation denied, aborting.");
                    exit(255);
                }
            }
            else
            {
                if(self::getBooleanInput("You are about to uninstall $package==$version, do you want to continue?") == false)
                {
                    self::logError("Installation denied, aborting.");
                    exit(255);
                }
            }

            /** @var PackageLockItem $PackageLockItem */
            $PackageLockItem = $PackageLock->Packages[$package];

            if($version == "all")
            {
                foreach($PackageLock->Packages[$package]->Versions as $version)
                {
                    self::logEvent("Uninstalling " . $PackageLockItem->PackageName . "==" . $version);
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

                self::logEvent("Uninstalling " . $PackageLockItem->PackageName . "==" . $version);
                IO::deleteDirectory($PackageLockItem->getPackagePath($version));
                $PackageLock->removePackage($package, $version);
            }

            self::logEvent("Updating Package Lock");
            ppm::savePackageLock($PackageLock);
        }

        /**
         * @param string $path
         * @throws InvalidPackageLockException
         */
        public static function installPackage(string $path)
        {
            if(file_exists($path) == false)
            {
                self::logError("The path '$path' does not exist");
                exit(255);
            }

            try
            {
                $PackageContents = ZiProto::decode(file_get_contents($path));
            }
            catch(Exception $e)
            {
                self::logError("The package cannot be opened correctly, the file may corrupted");
                exit(255);
            }

            if(isset($PackageContents['package']) == false)
            {
                self::logError("This package is missing information, is this a ppm package?");
                exit(255);
            }

            try
            {
                $PackageInformation = Package::fromArray($PackageContents['package']);
            }
            catch (Exception $e)
            {
                self::logError("There was an error while trying to read the package information", $e);
                exit(255);
            }

            if(System::isRoot() == false)
            {
                self::logError("This operation requires root privileges, please run ppm with 'sudo -H'");
                exit(255);
            }

            if(IO::writeTest(PathFinder::getMainPath(true)) == false)
            {
                self::logError("Write test failed, cannot write to the PPM installation directory");
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

            if(self::getBooleanInput("Do you want to install this package?") == false)
            {
                self::logError("Installation denied, aborting.");
                exit(255);
            }

            self::logEvent("Preparing installation");
            $PackageLock = ppm::getPackageLock();
            $package_name = $PackageInformation->Metadata->PackageName;
            $package_version =  $PackageInformation->Metadata->Version;

            if($PackageLock->packageExists($PackageInformation->Metadata->PackageName, $PackageInformation->Metadata->Version))
            {
                self::logError("Installation failed, the package " . $package_name . "==" . $package_version . " is already satisfied");
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
                        self:self::logError("Installation failed, This package requires the dependency '\e[37m" . $dependency->Package . "==\e[32m" .  $dependency->Version . "\e[91m' which is not installed");
                        exit(255);
                    }
                    else
                    {
                        self::logWarning("This package uses a non-required dependency '" . $dependency->Package . "==\e[32m" .  $dependency->Version . "\e[37m' which is not installed");
                    }
                }
            }


            self::logEvent("Installing " . $package_name . "==" . $package_version);
            $InstallationPath = PathFinder::getPackagePath(
                $PackageInformation->Metadata->PackageName, $PackageInformation->Metadata->Version, true
            );

            foreach($PackageContents["compiled_components"] as $component_name => $component)
            {
                if(stripos($component_name, "::"))
                {
                    $pieces = explode("::", $component_name);
                    $file_path = $InstallationPath . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pieces);
                    array_pop($pieces);
                    $path = $InstallationPath . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pieces);

                    if(file_exists($path) == false)
                    {
                        mkdir($path, 200, true);
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
                System::setPermissions($file_path, 200);
            }

            self::logEvent("Creating Package Data");

            $PackageDataPath = $InstallationPath . DIRECTORY_SEPARATOR . '.ppm';
            $PackageInformationPath = $PackageDataPath . DIRECTORY_SEPARATOR . 'PACKAGE';
            $PackageAutoloaderPath = $PackageDataPath . DIRECTORY_SEPARATOR . 'COMPONENTS';

            mkdir($PackageDataPath);
            file_put_contents(
                $PackageInformationPath, ZiProto::encode($PackageInformation->toArray()));
            file_put_contents(
                $PackageAutoloaderPath, ZiProto::encode(array_keys($PackageContents["compiled_components"])));

            self::logEvent("Updating Package Lock");
            $PackageLock->addPackage($PackageInformation);
            ppm::savePackageLock($PackageLock);
        }

        /**
         * @param string $path
         */
        public static function compilePackage(string $path)
        {
            $starting_time = microtime(true);
            self::logEvent("Loading from source");

            try
            {
                $Source = Source::loadSource($path);
            }
            catch (Exception $e)
            {
                self::logError("There was an error while trying to load from source", $e);
                exit(255);
            }

            self::logEvent("Compiling components");
            $CompiledComponents = $Source->compileComponents(true);

            self::logEvent("Packing package contents");
            $Contents = array(
                "type" => "ppm_package",
                "ppm_version" => PPM_VERSION,
                "package" => $Source->Package->toArray(),
                "compiled_components" => $CompiledComponents
            );
            $EncodedContents = ZiProto::encode($Contents);
            file_put_contents($Source->Package->Metadata->PackageName . ".ppm", $EncodedContents);

            $execution_time = (microtime(true) - $starting_time)/60;

            self::logEvent("Completed! Operation took $execution_time seconds");
            exit(0);
        }
    }