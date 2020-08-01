<?php


    namespace ppm\Utilities\CLI;


    use Exception;
    use ppm\Objects\Source;
    use ppm\Utilities\CLI;
    use ppm\Utilities\System;
    use ZiProto\ZiProto;

    /**
     * Class Compiler
     * @package ppm\Utilities\CLI
     */
    class Compiler
    {
        /**
         * Compiles package from source
         *
         * @param string $path
         * @param string $output_directory
         * @param bool $exit
         * @return string|null
         * @noinspection PhpUnused
         */
        public static function compilePackage(string $path, string $output_directory=null, bool $exit=true)
        {
            $starting_time = microtime(true);
            CLI::logEvent("Loading from source");

            try
            {
                $path = self::findSource($path);
                $Source = Source::loadSource($path);
            }
            catch (Exception $e)
            {
                CLI::logError("There was an error while trying to load from source", $e);
                exit(255);
            }

            CLI::logEvent("Compiling components");
            $CompiledComponents = $Source->compileComponents(true);

            CLI::logEvent("Packing package contents");
            $Contents = array(
                "type" => "ppm_package",
                "ppm_version" => PPM_VERSION,
                "package" => $Source->Package->toArray(),
                "compiled_components" => $CompiledComponents
            );
            $EncodedContents = ZiProto::encode($Contents);
            $compiled_file = $Source->Package->Metadata->PackageName . ".ppm";

            if($output_directory !== null)
            {
                if(isset(CLI::options()['directory']))
                {
                    $output_directory = CLI::options()['directory'];
                }
            }

            $output_file = null;
            if($output_directory !== null)
            {
                if(file_exists($output_directory) == false)
                {
                    mkdir($output_directory);
                }

                if(file_exists($output_directory) == false)
                {
                    CLI::logError("The directory " . $output_directory . " cannot be created");
                    exit(255);
                }

                $output_file = $output_directory . DIRECTORY_SEPARATOR . $compiled_file;
            }
            else
            {
                $output_file = $compiled_file;
            }

            file_put_contents($output_file, $EncodedContents);
            System::setPermissions($output_file, 0777);

            $execution_time = (microtime(true) - $starting_time)/60;

            CLI::logEvent("Completed! Operation took $execution_time seconds");

            if($exit)
            {
                exit(0);
            }

            return $output_file;
        }

        /**
         * Finds the directory containing package.json
         *
         * @param string $path
         * @return string
         */
        public static function findSource(string $path): string
        {
            $package_file = null;

            if(file_exists($path . DIRECTORY_SEPARATOR . "package.json"))
            {
                return $path;
            }

            if(file_exists($path . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "package.json"))
            {
                return $path . DIRECTORY_SEPARATOR . "src";
            }

            if(file_exists($path . DIRECTORY_SEPARATOR . ".ppm_package"))
            {
                $pointer = file_get_contents($path . DIRECTORY_SEPARATOR . ".ppm_package");
                $pointer = str_ireplace("/", DIRECTORY_SEPARATOR, $pointer);

                if(file_exists($path . DIRECTORY_SEPARATOR . $pointer))
                {
                    return $path . DIRECTORY_SEPARATOR . $pointer;
                }
            }

            if(file_exists($path . DIRECTORY_SEPARATOR . ".ppm"))
            {
                $pointer = file_get_contents($path . DIRECTORY_SEPARATOR . ".ppm");
                $pointer = str_ireplace("/", DIRECTORY_SEPARATOR, $pointer);

                if(file_exists($path . DIRECTORY_SEPARATOR . $pointer))
                {
                    return $path . DIRECTORY_SEPARATOR . $pointer;
                }
            }

            CLI::logError("Cannot locate package.json file, is this repo built for ppm?");
            exit(255);
        }
    }