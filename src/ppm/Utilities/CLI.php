<?php


    namespace ppm\Utilities;

    use Exception;
    use ppm\Exceptions\InvalidComponentException;
    use ppm\Exceptions\InvalidConfigurationException;
    use ppm\Exceptions\InvalidDependencyException;
    use ppm\Exceptions\InvalidPackageException;
    use ppm\Exceptions\MissingPackagePropertyException;
    use ppm\Exceptions\PathNotFoundException;
    use ppm\Objects\Package\Component;
    use ppm\Objects\Source;
    use ppm\ppm;
    use PpmParser\JsonDecoder;
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
                "compile::"
            );

            return getopt($options, $long_opts);
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
            print("\033[37m \033[33m--compile\033[37m=\"<path>\"" . PHP_EOL);
            print("\033[37m     Compiles a PHP library from source to a .ppm file" . PHP_EOL);
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
        public static function logError(string $message, Exception $exception)
        {
            print("\e[91m " . $message . "\e[37m" . PHP_EOL);
            print("\e[91m " . $exception->getMessage() . "\e[37m" . PHP_EOL);
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

            self::displayHelpMenu();
        }

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