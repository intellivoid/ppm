<?php


    namespace ppm\Utilities\CLI;


    use Exception;
    use ppm\Objects\Package;
    use ppm\Objects\Package\Component;
    use ppm\Objects\Source;
    use ppm\Utilities\CLI;
    use ppm\Utilities\System;
    use ppm\Utilities\Validate;
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

            CLI::logEvent("Validating package");
            self::validatePackage($Source->Package);

            CLI::logEvent("Compiling components");
            $CompiledComponents = $Source->compileComponents(true);

            CLI::logEvent("Packing extras");
            $PostInstallation = array();
            $PreInstallation = array();
            $MainExecution = null;
            $MainFile = null;

            // Process Post Installation scripts
            foreach($Source->Package->Configuration->PostInstallation as $script)
            {
                /** @noinspection DuplicatedCode */
                $Component = new Component();
                $Component->BaseDirectory = $Source->Path;
                $Component->File = $script;

                if(file_exists($Component->getPath()) == false)
                {
                    CLI::logError("Cannot find PostInstallation script '" . $Component->getPath() . "'");
                    exit(255);
                }

                $file_hash = hash("sha1", $script);
                $PostInstallation[$file_hash] = file_get_contents($Component->getPath());
            }

            // Process Pre Installation scripts
            foreach($Source->Package->Configuration->PreInstallation as $script)
            {
                /** @noinspection DuplicatedCode */
                $Component = new Component();
                $Component->BaseDirectory = $Source->Path;
                $Component->File = $script;

                if(file_exists($Component->getPath()) == false)
                {
                    CLI::logError("Cannot find PreInstallation script '" . $Component->getPath() . "'");
                    exit(255);
                }

                $file_hash = hash("sha1", $script);
                $PreInstallation[$file_hash] = file_get_contents($Component->getPath());
            }

            if($Source->Package->Configuration->Main !== null)
            {
                $MainExecution = $Source->Package->Configuration->Main->toArray();

                /** @noinspection DuplicatedCode */
                $Component = new Component();
                $Component->BaseDirectory = $Source->Path;
                $Component->File = $Source->Package->Configuration->Main->ExecutionPoint;

                if(file_exists($Component->getPath()) == false)
                {
                    CLI::logError("Cannot find the main execution pointer file '" . $Component->getPath() . "'");
                    exit(255);
                }

                $MainFile = file_get_contents($Component->getPath());;
            }

            CLI::logEvent("Packing package contents");
            $Contents = array(
                "type" => "ppm_package",
                "ppm_version" => PPM_VERSION,
                "package" => $Source->Package->toArray(),
                "compiled_components" => $CompiledComponents,
                "post_install" => $PostInstallation,
                "pre_install" => $PreInstallation,
                "main_file" => $MainFile,
                "main" => $MainExecution,
            );
            $EncodedContents = ZiProto::encode($Contents);
            $compiled_file = $Source->Package->Metadata->PackageName . ".ppm";
            $output_file = null;

            if($output_directory == null)
            {
                if(isset(CLI::options()['directory']))
                {
                    $output_directory = CLI::options()['directory'];
                }
            }

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

        /**
         * Validates the package information
         *
         * @param Package $package
         */
        public static function validatePackage(Package $package)
        {
            if(Validate::PackageName($package->Metadata->PackageName) == false)
            {
                CLI::logError("The package name is invalid, it must follow the convention as follows; 'com.example.package_name'");
                exit(255);
            }

            if(Validate::UserFriendlyPackageName($package->Metadata->Name) == false)
            {
                CLI::logError("The package friendly name is invalid, it must be 64 characters or less");
                exit(255);
            }

            if(Validate::Version($package->Metadata->Version) == false)
            {
                CLI::logError("The package name is invalid, it must follow the convention as follows; 'Major.Minor.Build.Revision'");
                exit(255);
            }

            if(strlen($package->Metadata->Description) == 0)
            {
                $package->Metadata->Description = null;
            }
            else
            {
                if(Validate::Description($package->Metadata->Description) == false)
                {
                    CLI::logError("The package description is invalid, it must be 1256 characters or less");
                    exit(255);
                }
            }

            if(strlen($package->Metadata->Author) == 0)
            {
                $package->Metadata->Author = null;
            }
            else
            {
                if(Validate::Author($package->Metadata->Author) == false)
                {
                    CLI::logError("The package author is invalid, it must be 1256 characters or less");
                    exit(255);
                }
            }

            if(strlen($package->Metadata->Organization) == 0)
            {
                $package->Metadata->Organization = null;
            }
            else
            {
                if(Validate::Organization($package->Metadata->Organization) == false)
                {
                    CLI::logError("The package organization is invalid, it must be 1256 characters or less");
                    exit(255);
                }
            }

            if(strlen($package->Metadata->URL) == 0)
            {
                $package->Metadata->URL = null;
            }
            else
            {
                if(Validate::Url($package->Metadata->URL) == false)
                {
                    CLI::logError("The package URL is invalid");
                    exit(255);
                }
            }
        }

    }