<?php


    namespace ppm\Utilities;

    use Exception;
    use ppm\Exceptions\InvalidPackageLockException;
    use ppm\Exceptions\VersionNotFoundException;
    use ppm\Utilities\CLI\Compiler;
    use ppm\Utilities\CLI\GithubVault;
    use ppm\Utilities\CLI\PackageManager;
    use ppm\Utilities\CLI\Runner;

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
                "github-add-pat",
                "github-remove-pat",
                "installed",
                "compile::",
                "main::",
                "args::",
                "directory::",
                "alias::",
                "token::",
                "install::",
                "branch::",
                "uninstall::",
                "version::"
            );

            return getopt($options, $long_opts);
        }

        /**
         * Gets a parameter from the CLI or user-input
         *
         * @param string $name
         * @param string $text
         * @param bool $require_parameter
         * @return string
         */
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

        /**
         * Prompts the user with a Yes/No input
         *
         * @param string $message
         * @return bool
         */
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

        /**
         * Gets input from stdout without the trailing linebreak
         *
         * @param string $message
         * @return string
         */
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
            self::displayIntro();
            print("\033[37m \033[33m--compile" . PHP_EOL);
            print("\033[37m     Compiles the current PHP library/program from source to a .ppm file" . PHP_EOL);
            print("\033[37m \033[33m--compile\033[37m=\"<path>\"" . PHP_EOL);
            print("\033[37m     Compiles a PHP library/program from source to a .ppm file" . PHP_EOL);
            print("\033[37m \033[33m--install\033[37m=\"<path>\"" . PHP_EOL);
            print("\033[37m     Installs a .ppm package to the system" . PHP_EOL);
            print("\033[37m \033[33m--install\033[37m=\"<alias>@github/<organization>/<repo>\" \e[33m--branch\e[37m=\"<optional_branch>\"" . PHP_EOL);
            print("\033[37m     Compiles and installs from a GitHub repo" . PHP_EOL);
            print("\033[37m \033[33m--uninstall\033[37m=\"<package_name>\"" . PHP_EOL);
            print("\033[37m     Completely uninstalls a installed package from the system" . PHP_EOL);
            print("\033[37m \033[33m--uninstall\033[37m=\"<package_name>\" \e[33m--version\e[37m=\"<version>\"" . PHP_EOL);
            print("\033[37m     Uninstalls a specific version of the package from the system" . PHP_EOL);
            print("\033[37m \033[33m--installed" . PHP_EOL);
            print("\033[37m     Lists all the installed packages on the system" . PHP_EOL);
            print("\033[37m \033[33m--main\e[37m=\"<package_name>\"" . PHP_EOL);
            print("\033[37m     Executes the main execution point of a package" . PHP_EOL);
            print("\033[37m \033[33m--main\e[37m=\"<package_name>\" \e[33m--version\e[37m=\"<version>\"" . PHP_EOL);
            print("\033[37m     Executes the execution point of a specific version of a package" . PHP_EOL);
            print("\033[37m \033[33m--main\e[37m=\"<package_name>\" \e[33m--args\e[37m=\"<arguments>\"" . PHP_EOL);
            print("\033[37m     Executes the execution point of a package, passing on optional commandline arguments" . PHP_EOL . PHP_EOL);

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
            if(isset(self::options()['compile']))
            {
                if(strlen(self::options()['compile']) == 0)
                {
                    Compiler::compilePackage(getcwd());
                }
                else
                {
                    Compiler::compilePackage(self::options()['compile']);
                }

                return;
            }

            if(isset(self::options()['install']))
            {
                try
                {
                    PackageManager::installPackage(self::options()['install']);
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

                        PackageManager::uninstallPackage(self::options()['uninstall'], self::options()['version']);
                    }
                    else
                    {
                        PackageManager::uninstallPackage(self::options()['uninstall']);
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
                    PackageManager::getInstalledPackages();
                }
                catch (InvalidPackageLockException $e)
                {
                    self::logError("Failed to list installed packages, Invalid Package Lock Error", $e);
                    exit(255);
                }

                return;
            }

            if(isset(self::options()['main']))
            {
                try
                {
                    $version = "latest";
                    $args = "latest";

                    if(isset(self::options()["version"]))
                    {
                        $version = self::options()["version"];
                    }

                    if(isset(self::options()["args"]))
                    {
                        $args = self::options()["args"];
                    }

                    Runner::executePackageMain(self::options()['main'], $version, $args);
                }
                catch (InvalidPackageLockException $e)
                {
                    self::logError("Failed to execute, invalid Package Lock Error", $e);
                    exit(255);
                }
                catch (VersionNotFoundException $e)
                {
                    self::logError("Failed to execute, version not found", $e);
                    exit(255);
                }

                return;
            }

            if(isset(self::options()["github-add-pat"]))
            {
                GithubVault::githubAddPersonalAccessKey();
                return;
            }

            if(isset(self::options()["github-remove-pat"]))
            {
                GithubVault::githubRemovePersonalAccessKey();
                return;
            }

            self::displayHelpMenu();
        }
    }