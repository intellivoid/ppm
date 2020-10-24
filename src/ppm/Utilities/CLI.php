<?php


    namespace ppm\Utilities;

    use Exception;
    use ppm\Abstracts\CompilerFlags;
    use ppm\Exceptions\InvalidPackageLockException;
    use ppm\Exceptions\VersionNotFoundException;
    use ppm\Utilities\CLI\Compiler;
    use ppm\Utilities\CLI\GithubVault;
    use ppm\Utilities\CLI\PackageManager;
    use ppm\Utilities\CLI\Runner;
    use ppm\Utilities\CLI\Tools;

    /**
     * Class CLI
     * @package ppm\Utilities
     */
    class CLI
    {
        /**
         * When enabled the CLI will spit out verbose information
         *
         * @var bool
         */
        public static $VerboseMode = false;

        /**
         * When enabled, printing functions will stream data to stdout
         *
         * @var bool
         */
        public static $Stdout = false;

        /**
         * The current flags set for the compiler
         *
         * @var array
         */
        public static $CompilerFlags = array();

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
                "github-set-default",
                "installed",
                "compile::",
                "sdc::",
                "main::",
                "args::",
                "directory::",
                "alias::",
                "token::",
                "install::",
                "update::",
                "branch::",
                "uninstall::",
                "generate-package::",
                "recreate",
                "generate-autoloader::",
                "version::",
                "package-name::",
                "name::",
                "description::",
                "author::",
                "organization::",
                "url::",
                "verbose",
                "v",
                "fix-conflict",
                "clear-cache",
                "native",
                "lerror",
                "lwarning",
                "bcerror",
                "bcwarning",
                "cwarning",
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
                    exit(1);
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
         * Sets the compiler flags from user-defined or by default
         */
        public static function setCompilerFlags()
        {
            // Linting flag
            if(isset(CLI::options()["lerror"]))
            {
                self::$CompilerFlags[] = CompilerFlags::LintingError;
            }
            elseif(isset(CLI::options()["lwarning"]))
            {
                self::$CompilerFlags[] = CompilerFlags::LintingWarning;
            }
            else
            {
                self::$CompilerFlags[] = CompilerFlags::LintingWarning;
            }

            // Byte compiling flags
            if(isset(CLI::options()["bcerror"]))
            {
                self::$CompilerFlags[] = CompilerFlags::ByteCompilerError;
            }
            elseif(isset(CLI::options()["bcwarning"]))
            {
                self::$CompilerFlags[] = CompilerFlags::ByteCompilerWarning;
            }
            else
            {
                self::$CompilerFlags[] = CompilerFlags::ByteCompilerWarning;
            }

            // General compiling flags
            if(isset(CLI::options()["cerror"]))
            {
                self::$CompilerFlags[] = CompilerFlags::CompilerError;
            }
            elseif(isset(CLI::options()["cwarning"]))
            {
                self::$CompilerFlags[] = CompilerFlags::CompilerWarning;
            }
            else
            {
                self::$CompilerFlags[] = CompilerFlags::CompilerError;
            }
        }

        /**
         * Prints out the main CLI Intro
         */
        public static function displayIntro()
        {
            if(self::$Stdout == false)
            {
                return;
            }

            $dark_colors = ['31', '32', '33', '34', '35', '36'];
            $light_colors = ['91', '92', '93', '94', '95', '96'];

            if(rand(0,1) == 1)
            {
                $sc = $dark_colors;
            }
            else
            {
                $sc = $light_colors;
            }

            print("\e[" . $sc[array_rand($sc, 1)] . "m ________  ________  _____ ______      " . PHP_EOL);
            print("\e[" . $sc[array_rand($sc, 1)] . "m|\   __  \|\   __  \|\   _ \  _   \    " . PHP_EOL);
            print("\e[" . $sc[array_rand($sc, 1)] . "m\ \  \|\  \ \  \|\  \ \  \\\__\ \   \   " . PHP_EOL);
            print("\e[" . $sc[array_rand($sc, 1)] . "m \ \   ____\ \   ____\ \  \\|__| \   \  " . PHP_EOL);
            print("\e[" . $sc[array_rand($sc, 1)] . "m  \ \  \___|\ \  \___|\ \  \    \ \  \ " . PHP_EOL);
            print("\e[" . $sc[array_rand($sc, 1)] . "m   \ \__\    \ \__\    \ \__\    \ \__\\" . PHP_EOL);
            print("\e[" . $sc[array_rand($sc, 1)] . "m    \|__|     \|__|     \|__|     \|__|" . PHP_EOL);
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
            if(self::$Stdout == false)
            {
                return;
            }

            self::displayIntro();
            print("Main options" . PHP_EOL);
            print("\033[37m \033[33m--compile" . PHP_EOL);
            print("\033[37m     Compiles the current PHP library/program from source to a .ppm file" . PHP_EOL);
            print("\033[37m \033[33m--compile\033[37m=\"<path>\"" . PHP_EOL);
            print("\033[37m     Compiles a PHP library/program from source to a .ppm file" . PHP_EOL);
            print("\033[37m \033[33m--sdc\033[37m=\"<path>\"" . PHP_EOL);
            print("\033[37m     Semi-decompiles a compiled package (.ppm) and prints out all the available information" . PHP_EOL);
            print("\033[37m \033[33m--install\033[37m=\"<path>\"" . PHP_EOL);
            print("\033[37m     Installs a .ppm package to the system" . PHP_EOL);
            print("\033[37m \033[33m--install\033[37m=\"<path>\" \e[33m--fix-conflict" . PHP_EOL);
            print("\033[37m     Installs a .ppm package to the system and uninstalls the conflicting package" . PHP_EOL);
            print("\033[37m \033[33m--install\033[37m=\"<alias>@github/<organization>/<repo>\" \e[33m--branch\e[37m=\"<optional_branch>\"" . PHP_EOL);
            print("\033[37m     Compiles and installs from a GitHub repo" . PHP_EOL);
            print("\033[37m \033[33m--install\033[37m=\"<vendor>@composer/<package>\"" . PHP_EOL);
            print("\033[37m     Compiles and installs from composer" . PHP_EOL);
            print("\033[37m \033[33m--install\033[37m=\"<vendor>@composer/<package>\" \e[33m--native" . PHP_EOL);
            print("\033[37m     Compiles and installs from composer and it's dependencies as native packages (unstable)" . PHP_EOL);
            print("\033[37m \033[33m--uninstall\033[37m=\"<package_name>\"" . PHP_EOL);
            print("\033[37m     Completely uninstalls a installed package from the system" . PHP_EOL);
            print("\033[37m \033[33m--uninstall\033[37m=\"<package_name>\" \e[33m--version\e[37m=\"<version>\"" . PHP_EOL);
            print("\033[37m     Uninstalls a specific version of the package from the system" . PHP_EOL);
            print("\033[37m \033[33m--update" . PHP_EOL);
            print("\033[37m     Updates all installed packages from a remote source" . PHP_EOL);
            print("\033[37m \033[33m--update\033[37m=\"<package_name>\"" . PHP_EOL);
            print("\033[37m     Updates the specified package from the remote source" . PHP_EOL);
            print("\033[37m \033[33m--installed" . PHP_EOL);
            print("\033[37m     Lists all the installed packages on the system" . PHP_EOL);
            print("\033[37m \033[33m--main\e[37m=\"<package_name>\"" . PHP_EOL);
            print("\033[37m     Executes the main execution point of a package" . PHP_EOL);
            print("\033[37m \033[33m--main\e[37m=\"<package_name>\" \e[33m--version\e[37m=\"<version>\"" . PHP_EOL);
            print("\033[37m     Executes the execution point of a specific version of a package" . PHP_EOL);
            print("\033[37m \033[33m--main\e[37m=\"<package_name>\" \e[33m--args\e[37m=\"<arguments>\"" . PHP_EOL);
            print("\033[37m     Executes the execution point of a package, passing on optional commandline arguments" . PHP_EOL . PHP_EOL);

            print("\033[37m \033[33m--generate-package" . PHP_EOL);
            print("\033[37m     Generates/updates a package.json file from your project's source code (Current directory)" . PHP_EOL);
            print("\033[37m \033[33m--generate-package\e[37m=\"<path>\"" . PHP_EOL);
            print("\033[37m     Generates a package.json file from your project's source code" . PHP_EOL);
            print("\033[37m \033[33m--generate-package\e[37m=\"<path>\" \e[33m--recreate" . PHP_EOL);
            print("\033[37m     Generates/recreates a package.json from scratch file from your project's source code" . PHP_EOL . PHP_EOL);

            print("\033[37m \033[33m--github-add-pat \e[33m--alias\e[37m=\"<alias>\" \e[33m--token\e[37m=\"<personal_access_token>\"" . PHP_EOL);
            print("\033[37m     Adds a GitHub personal access key to be used with the GitHub API (Secured)" . PHP_EOL);
            print("\033[37m \033[33m--github-remove-pat \e[33m--alias\e[37m=\"<alias>\"" . PHP_EOL);
            print("\033[37m     Removes a GitHub personal access key" . PHP_EOL . PHP_EOL);
            print("\033[37m \033[33m--github-set-default \e[33m--alias\e[37m=\"<alias>\"" . PHP_EOL);
            print("\033[37m     Updates the current default profile to the one specified by alias" . PHP_EOL . PHP_EOL);

            print("\033[37m \033[33m--clear-cache" . PHP_EOL);
            print("\033[37m     Clears PPM cache from disk" . PHP_EOL);

            print("Extra options" . PHP_EOL);
            print("\033[37m \033[33m--verbose -v" . PHP_EOL);
            print("\033[37m     Reports logging information in verbose" . PHP_EOL . PHP_EOL);

            print("Compiler flags/options" . PHP_EOL);
            print("\033[37m \033[33m--alm\e[37m=\"<method>\"" . PHP_EOL);
            print("\033[37m     Overrides the the autoload generator method" . PHP_EOL);
            print("\033[37m \033[33m--lerror" . PHP_EOL);
            print("\033[37m     Treats linting failures as errors which stops the operation" . PHP_EOL);
            print("\033[37m \033[33m--lwarning" . PHP_EOL);
            print("\033[37m     Treats linting failures as warnings" . PHP_EOL);
            print("\033[37m \033[33m--bcerror" . PHP_EOL);
            print("\033[37m     Treats failure to compile as an error instead of falling to byte compiling" . PHP_EOL);
            print("\033[37m \033[33m--bcwarning" . PHP_EOL);
            print("\033[37m     Treats failure to compile as a warning and falls to byte compiling instead" . PHP_EOL);
            print("\033[37m \033[33m--cwarning" . PHP_EOL);
            print("\033[37m     Treats compiling errors as warnings, this can still package the component but might not be able to generate a proper autoloader" . PHP_EOL);
            print("\033[37m \033[33m--cerror" . PHP_EOL);
            print("\033[37m     Treats compiling errors as errors and halts the operation (default)" . PHP_EOL);
        }

        /**
         * Logs an event
         *
         * @param string $message
         * @param bool $newline
         */
        public static function logEvent(string $message, bool $newline=true)
        {
            if(self::$Stdout == false)
            {
                return;
            }

            $timestamp = gmdate("[y:m:d h:i:s]");

            if($newline)
            {
                print("\e[37m$message" . PHP_EOL);
            }
            else
            {
                print("\e[37m$message");
            }
        }

        /**
         * Logs a verbose event
         *
         * @param string $message
         * @param bool $newline
         */
        public static function logVerboseEvent(string $message, bool $newline=true)
        {
            if(self::$Stdout == false)
            {
                return;
            }

            if(self::$VerboseMode == false)
            {
                return;
            }

            $timestamp = gmdate("[y:m:d h:i:s]");

            if($newline)
            {
                print("\e[37m$message" . PHP_EOL);
            }
            else
            {
                print("\e[37m$message");
            }
        }

        /**
         * @param string $message
         * @param Exception|null $exception
         */
        public static function logError(string $message, Exception $exception=null)
        {
            if(self::$Stdout == false)
            {
                return;
            }

            print("\e[91m" . $message . "\e[37m" . PHP_EOL);
            if(is_null($exception) == false)
            {
                print("\e[91m" . $exception->getMessage() . "\e[37m" . PHP_EOL);
            }
        }

        /**
         * @param string $message
         */
        public static function logWarning(string $message)
        {
            if(self::$Stdout == false)
            {
                return;
            }

            print("\e[33mWARNING: \e[37m" . $message . "\e[37m" . PHP_EOL);

        }

        /**
         * Processes the command-line options
         */
        public static function start()
        {
            self::$Stdout = true;
            self::setCompilerFlags();

            if(isset(self::options()["verbose"]))
            {
                self::$VerboseMode = true;
            }

            if(isset(self::options()["v"]))
            {
                self::$VerboseMode = true;
            }

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

            if(isset(self::options()['sdc']))
            {
                Compiler::semiDecompilePackage(self::options()['sdc']);
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
                    exit(1);
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
                    exit(1);
                }
                catch (VersionNotFoundException $e)
                {
                    self::logError("Failed to uninstall package, Version not found", $e);
                    exit(1);
                }

                return;
            }

            if(isset(self::options()['update']))
            {
                try
                {
                    if(strlen(self::options()['update']) == 0)
                    {
                        PackageManager::updateAllPackages();
                    }
                    else
                    {
                        PackageManager::updatePackage(self::options()['update']);
                    }
                }
                catch (InvalidPackageLockException $e)
                {
                    self::logError("Failed to update package, Invalid Package Lock Error", $e);
                    exit(1);
                }
                catch (VersionNotFoundException $e)
                {
                    self::logError("Failed to update package (probably a bug), Version not found", $e);
                    exit(1);
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
                    exit(1);
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
                    exit(1);
                }
                catch (VersionNotFoundException $e)
                {
                    self::logError("Failed to execute, version not found", $e);
                    exit(1);
                }

                return;
            }

            if(isset(self::options()['generate-package']))
            {
                $recreate = false;

                if(isset(self::options()['recreate']))
                {
                    $recreate = true;
                }

                if(strlen(self::options()['generate-package']) == 0)
                {
                    Tools::generatePackageJson(getcwd(), $recreate);
                }
                else
                {
                    Tools::generatePackageJson(self::options()['generate-package'], $recreate);
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

            if(isset(self::options()["github-set-default"]))
            {
                GithubVault::githubSetDefaultProfile();
                return;
            }

            if(isset(self::options()["clear-cache"]))
            {
                Tools::clearCache();
                return;
            }

            self::displayHelpMenu();
        }
    }