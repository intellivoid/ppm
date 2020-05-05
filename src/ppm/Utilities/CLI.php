<?php


    namespace ppm\Utilities;

    use ppm\Objects\Package\Component;
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
            $Source = ppm::loadSource($path);
            $CompiledComponents = array();

            /** @var Component $component */
            foreach($Source->Package->Components as $component)
            {
                self::logEvent("Processing " . $component->getPath());
                self::logEvent("Parsing ", false);
                $ParsedComponent = $component->parse();
                self::logEvent("Encoding ", false);
                $Structure = json_encode($ParsedComponent);
                self::logEvent("Compiling ", false);
                $Compiled = ZiProto::encode(json_decode($Structure, true));
                self::logEvent("Success" . PHP_EOL);
                $CompiledComponents[$component->File] = $Compiled;
            }

            file_put_contents("out.ppm", ZiProto::encode($CompiledComponents));
        }
    }