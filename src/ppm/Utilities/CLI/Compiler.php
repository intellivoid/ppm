<?php


    namespace ppm\Utilities\CLI;


    use Exception;
    use ppm\Abstracts\CompilerFlags;
    use ppm\Objects\Package;
    use ppm\Objects\Package\Component;
    use ppm\Objects\Source;
    use ppm\Utilities\CLI;
    use ppm\Utilities\System;
    use ppm\Utilities\Validate;
    use PpmZiProto\ZiProto;

    /**
     * Class Compiler
     * @package ppm\Utilities\CLI
     */
    class Compiler
    {
        /**
         * Gets the linting flag state
         *
         * @return int
         */
        public static function getLintingFlag(): int
        {
            if(in_array(CompilerFlags::LintingWarning, CLI::$CompilerFlags))
            {
                return CompilerFlags::LintingWarning;
            }

            if(in_array(CompilerFlags::LintingError, CLI::$CompilerFlags))
            {
                return CompilerFlags::LintingError;
            }

            return CompilerFlags::LintingWarning;
        }

        /**
         * Gets the byte compiler state
         *
         * @return int
         */
        public static function getByteCompilingFlag(): int
        {
            if(in_array(CompilerFlags::ByteCompilerWarning, CLI::$CompilerFlags))
            {
                return CompilerFlags::ByteCompilerWarning;
            }

            if(in_array(CompilerFlags::ByteCompilerError, CLI::$CompilerFlags))
            {
                return CompilerFlags::ByteCompilerError;
            }

            return CompilerFlags::ByteCompilerWarning;
        }

        /**
         * Gets the general compiler flag
         *
         * @return int
         */
        public static function getCompilerFlag(): int
        {
            if(in_array(CompilerFlags::CompilerError, CLI::$CompilerFlags))
            {
                return CompilerFlags::CompilerError;
            }

            if(in_array(CompilerFlags::CompilerWarning, CLI::$CompilerFlags))
            {
                return CompilerFlags::CompilerWarning;
            }

            return CompilerFlags::CompilerError;
        }

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
                CLI::logVerboseEvent("Source path => " . $path);
                $Source = Source::loadSource($path);
            }
            catch (Exception $e)
            {
                CLI::logError("There was an error while trying to load from source", $e);
                exit(1);
            }

            CLI::logEvent("Validating package");
            self::validatePackage($Source->Package);

            CLI::logEvent("Compiling components");

            $cwarning_flag = false;
            if(self::getCompilerFlag() == CompilerFlags::CompilerWarning)
            {
                CLI::logVerboseEvent("All compiler errors will be treated as warnings");
                $cwarning_flag = true;
            }

            $CompiledComponents = $Source->compileComponents($cwarning_flag);

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

                CLI::logVerboseEvent("== Post Installation Script ==");
                CLI::logVerboseEvent("Base Directory => " . $Component->BaseDirectory);
                CLI::logVerboseEvent("File => " . $Component->File);

                if(file_exists($Component->getPath()) == false)
                {
                    CLI::logError("Cannot find PostInstallation script '" . $Component->getPath() . "'");
                    exit(1);
                }

                $file_hash = hash("sha1", $script);
                $PostInstallation[$file_hash] = file_get_contents($Component->getPath());
                CLI::logVerboseEvent("Script hash sha1 => " . $file_hash);
            }

            // Process Pre Installation scripts
            foreach($Source->Package->Configuration->PreInstallation as $script)
            {
                /** @noinspection DuplicatedCode */
                $Component = new Component();
                $Component->BaseDirectory = $Source->Path;
                $Component->File = $script;

                CLI::logVerboseEvent("== Pre Installation Script ==");
                CLI::logVerboseEvent("Base Directory => " . $Component->BaseDirectory);
                CLI::logVerboseEvent("File => " . $Component->File);

                if(file_exists($Component->getPath()) == false)
                {
                    CLI::logError("Cannot find PreInstallation script '" . $Component->getPath() . "'");
                    exit(1);
                }

                $file_hash = hash("sha1", $script);
                $PreInstallation[$file_hash] = file_get_contents($Component->getPath());
                CLI::logVerboseEvent("Script hash sha1 => " . $file_hash);
            }

            if($Source->Package->Configuration->Main !== null)
            {
                $MainExecution = $Source->Package->Configuration->Main->toArray();

                /** @noinspection DuplicatedCode */
                $Component = new Component();
                $Component->BaseDirectory = $Source->Path;
                $Component->File = $Source->Package->Configuration->Main->ExecutionPoint;

                CLI::logVerboseEvent("== Main Execution Point ==");
                CLI::logVerboseEvent("Base Directory => " . $Component->BaseDirectory);
                CLI::logVerboseEvent("File => " . $Component->File);

                if(file_exists($Component->getPath()) == false)
                {
                    CLI::logError("Cannot find the main execution pointer file '" . $Component->getPath() . "'");
                    exit(1);
                }

                $MainFile = file_get_contents($Component->getPath());;
            }

            $PackedFiles = array();
            if($Source->Package->Files !== null)
            {
                foreach($Source->Package->Files as $file)
                {
                    $file_path = $Source->Path . DIRECTORY_SEPARATOR . str_ireplace("/", DIRECTORY_SEPARATOR, $file);

                    if(file_exists($file_path) == false)
                    {
                        CLI::logError("Cannot find the file '" . $file_path . "'");
                        exit(1);
                    }

                    $PackedFiles[$file] = file_get_contents($file_path);
                }
            }

            CLI::logEvent("Packing package contents");
            $Contents = array(
                "type" => "ppm_package",
                "ppm_version" => PPM_VERSION,
                "package" => $Source->Package->toArray(),
                "compiled_components" => $CompiledComponents["compiled_components"],
                "byte_compiled" => $CompiledComponents["byte_compiled"],
                "raw" => $CompiledComponents["raw"],
                "files" => $PackedFiles,
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
                    exit(1);
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
            CLI::logVerboseEvent("Attempting to find package.json");

            if(file_exists($path . DIRECTORY_SEPARATOR . "package.json"))
            {
                CLI::logVerboseEvent("Found " . $path . DIRECTORY_SEPARATOR . "package.json");
                return $path;
            }

            if(file_exists($path . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "package.json"))
            {
                CLI::logVerboseEvent("Found " . $path . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "package.json");
                return $path . DIRECTORY_SEPARATOR . "src";
            }

            if(file_exists($path . DIRECTORY_SEPARATOR . ".ppm_package"))
            {
                CLI::logVerboseEvent("Found path pointer (.ppm_package) " . $path . DIRECTORY_SEPARATOR . ".ppm_package");
                $pointer = file_get_contents($path . DIRECTORY_SEPARATOR . ".ppm_package");
                $pointer = str_ireplace("/", DIRECTORY_SEPARATOR, $pointer);
                $pointer = trim(preg_replace('/\s\s+/', ' ', $pointer));

                if(file_exists($path . DIRECTORY_SEPARATOR . $pointer))
                {
                    CLI::logVerboseEvent("Found " . $path . DIRECTORY_SEPARATOR . $pointer);
                    return $path . DIRECTORY_SEPARATOR . $pointer;
                }
            }

            if(file_exists($path . DIRECTORY_SEPARATOR . ".ppm"))
            {
                CLI::logVerboseEvent("Found path pointer (.ppm) " . $path . DIRECTORY_SEPARATOR . ".ppm");
                $pointer = file_get_contents($path . DIRECTORY_SEPARATOR . ".ppm");
                $pointer = str_ireplace("/", DIRECTORY_SEPARATOR, $pointer);
                $pointer = trim(preg_replace('/\s\s+/', ' ', $pointer));

                if(file_exists($path . DIRECTORY_SEPARATOR . $pointer))
                {
                    CLI::logVerboseEvent("Found " . $path . DIRECTORY_SEPARATOR . $pointer);
                    return $path . DIRECTORY_SEPARATOR . $pointer;
                }
            }

            CLI::logError("Cannot locate package.json file, is this repo built for ppm?");
            exit(1);
        }

        /**
         * Validates the package information
         *
         * @param Package $package
         */
        public static function validatePackage(Package $package)
        {
            CLI::logVerboseEvent("Validating metadata entry 'package_name'");
            if(Validate::PackageName($package->Metadata->PackageName) == false)
            {
                CLI::logError("The package name is invalid, it must follow the convention as follows; 'com.example.package_name'");
                exit(1);
            }

            CLI::logVerboseEvent("Validating metadata entry 'name'");
            if(Validate::UserFriendlyPackageName($package->Metadata->Name) == false)
            {
                CLI::logError("The package friendly name is invalid, it must be 64 characters or less");
                exit(1);
            }

            CLI::logVerboseEvent("Validating metadata entry 'version'");
            if(Validate::Version($package->Metadata->Version) == false)
            {
                CLI::logError("The package name is invalid, it must follow the convention as follows; 'Major.Minor.Build.Revision'");
                exit(1);
            }

            CLI::logVerboseEvent("Validating metadata entry 'description'");
            if(strlen($package->Metadata->Description) == 0)
            {
                CLI::logWarning("This package metadata contains no description");
                $package->Metadata->Description = null;
            }
            else
            {
                if(Validate::Description($package->Metadata->Description) == false)
                {
                    CLI::logError("The package description is invalid, it must be 1256 characters or less");
                    exit(1);
                }
            }

            CLI::logVerboseEvent("Validating metadata entry 'author'");
            if(strlen($package->Metadata->Author) == 0)
            {
                CLI::logWarning("The package metadata contains no author");
                $package->Metadata->Author = null;
            }
            else
            {
                if(Validate::Author($package->Metadata->Author) == false)
                {
                    CLI::logError("The package author is invalid, it must be 1256 characters or less");
                    exit(1);
                }
            }

            CLI::logVerboseEvent("Validating metadata entry 'organization'");
            if(strlen($package->Metadata->Organization) == 0)
            {
                $package->Metadata->Organization = null;
            }
            else
            {
                if(Validate::Organization($package->Metadata->Organization) == false)
                {
                    CLI::logError("The package organization is invalid, it must be 1256 characters or less");
                    exit(1);
                }
            }

            CLI::logVerboseEvent("Validating metadata entry 'url'");
            if(strlen($package->Metadata->URL) == 0)
            {
                CLI::logWarning("The package metadata contains no URL");
                $package->Metadata->URL = null;
            }
            else
            {
                if(Validate::Url($package->Metadata->URL) == false)
                {
                    CLI::logError("The package URL is invalid");
                    exit(1);
                }
            }
        }

    }