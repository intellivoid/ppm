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
         * Prints out detailed information about a compiled package
         *
         * @param string $path
         */
        public static function semiDecompilePackage(string $path)
        {
            try
            {
                $PackageContents = ZiProto::decode(file_get_contents($path));
            }
            catch(Exception $e)
            {
                CLI::logError("The package cannot be opened correctly, the file may corrupted");
                exit(1);
            }

            if(isset($PackageContents['package']) == false)
            {
                CLI::logError("This package is missing information, is this a ppm package?");
                exit(1);
            }

            try
            {
                $PackageInformation = Package::fromArray($PackageContents['package']);
            }
            catch (Exception $e)
            {
                CLI::logError("There was an error while trying to read the package information", $e);
                exit(1);
            }

            print($PackageInformation->Metadata->PackageName . " v" . $PackageInformation->Metadata->Version . PHP_EOL);

            print("==== STRUCTURE ====" . PHP_EOL);
            foreach($PackageContents as $key => $value)
            {
                print(" [" . $key . "]::" . gettype($value) . PHP_EOL);
            }
            print(PHP_EOL);

            print("==== METADATA ====" . PHP_EOL);
            foreach($PackageInformation->Metadata->toArray() as $key => $value)
            {
                print(" $key: " . json_encode($value, JSON_UNESCAPED_SLASHES) . PHP_EOL);
            }
            print(PHP_EOL);

            print("==== DEPENDENCIES ====" . PHP_EOL);
            if(count($PackageInformation->Dependencies) > 0)
            {
                foreach($PackageInformation->Dependencies as $dependency)
                {
                    foreach($dependency->toArray() as $key => $value)
                    {
                        print(" $key: " . json_encode($value, JSON_UNESCAPED_SLASHES) . PHP_EOL);
                    }

                    print(PHP_EOL);
                }
            }
            else
            {
                print("No dependencies specified" . PHP_EOL);
            }
            print(PHP_EOL);

            print("==== CONFIGURATION ====" . PHP_EOL);
            if($PackageInformation->Configuration->Main !== null)
            {
                print("main.name: " . json_encode($PackageInformation->Configuration->Main->Name, JSON_UNESCAPED_SLASHES) . PHP_EOL);
                print("main.create_symlink: " . json_encode($PackageInformation->Configuration->Main->CreateSymlink, JSON_UNESCAPED_SLASHES) . PHP_EOL);
                print("main.execution_point: " . json_encode($PackageInformation->Configuration->Main->ExecutionPoint, JSON_UNESCAPED_SLASHES) . PHP_EOL);
            }
            else
            {
                print("No main execution specified" . PHP_EOL);
            }

            if($PackageInformation->Configuration->AutoLoadMethod !== null)
            {
                print("Autoload Method: " . json_encode($PackageInformation->Configuration->AutoLoadMethod, JSON_UNESCAPED_SLASHES) . PHP_EOL);
            }
            else
            {
                print("No autoload method specified" . PHP_EOL);
            }

            if($PackageInformation->Configuration->PostInstallation !== null)
            {
                if(count($PackageInformation->Configuration->PostInstallation) > 0)
                {
                    print("Post installation scripts: " . json_encode($PackageInformation->Configuration->PostInstallation, JSON_UNESCAPED_SLASHES) . PHP_EOL);
                }
                else
                {
                    print("No post installation scripts specified" . PHP_EOL);
                }
            }
            else
            {
                print("No post installation field available" . PHP_EOL);
            }

            if($PackageInformation->Configuration->PreInstallation !== null)
            {
                if(count($PackageInformation->Configuration->PreInstallation) > 0)
                {
                    print("Pre installation scripts: " . json_encode($PackageInformation->Configuration->PreInstallation, JSON_UNESCAPED_SLASHES) . PHP_EOL);
                }
                else
                {
                    print("No pre installation scripts specified" . PHP_EOL);
                }
            }
            else
            {
                print("No pre installation field available" . PHP_EOL);
            }

            print(PHP_EOL . json_encode($PackageInformation->Configuration->toArray(), JSON_UNESCAPED_SLASHES) . PHP_EOL);
            print(PHP_EOL);

            print("==== COMPONENTS ====" . PHP_EOL);
            if(isset($PackageContents["compiled_components"]))
            {
                if(count($PackageContents["compiled_components"]) > 0)
                {
                    foreach($PackageContents["compiled_components"] as $key => $value)
                    {
                        print(json_encode($key, JSON_UNESCAPED_SLASHES) . " " . strlen($value) . " byte(s)" . PHP_EOL);
                    }
                }
                else
                {
                    print("No items" . PHP_EOL);
                }
            }
            else
            {
                print("Header not present" . PHP_EOL);
            }

            print(PHP_EOL);

            print("==== BYTE COMPILED ====" . PHP_EOL);
            if(isset($PackageContents["byte_compiled"]))
            {
                if(count($PackageContents["byte_compiled"]) > 0)
                {
                    foreach($PackageContents["byte_compiled"] as $key => $value)
                    {
                        print(json_encode($key, JSON_UNESCAPED_SLASHES) . " " . strlen($value) . " byte(s)" . PHP_EOL);
                    }
                }
                else
                {
                    print("No items" . PHP_EOL);
                }
            }
            else
            {
                print("Header not present" . PHP_EOL);
            }
            print(PHP_EOL);

            print("==== RAW ASSETS ====" . PHP_EOL);
            if(isset($PackageContents["raw"]))
            {
                if(count($PackageContents["raw"]) > 0)
                {
                    foreach($PackageContents["raw"] as $key => $value)
                    {
                        print(json_encode($key, JSON_UNESCAPED_SLASHES) . " " . strlen($value) . " byte(s)" . PHP_EOL);
                    }
                }
                else
                {
                    print("No items" . PHP_EOL);
                }
            }
            else
            {
                print("Header not present" . PHP_EOL);
            }
            print(PHP_EOL);

            print("==== FILE ASSETS ====" . PHP_EOL);
            if(isset($PackageContents["file"]))
            {
                if(count($PackageContents["file"]) > 0)
                {
                    foreach($PackageContents["file"] as $key => $value)
                    {
                        print(json_encode($key, JSON_UNESCAPED_SLASHES) . " " . strlen($value) . " byte(s)" . PHP_EOL);
                    }
                }
                else
                {
                    print("No items" . PHP_EOL);
                }
            }
            else
            {
                print("Header not present" . PHP_EOL);
            }
            print(PHP_EOL);

            print("==== COMPILER INFO ====" . PHP_EOL);
            print("Size: " . strlen(file_get_contents($path)) . " byte(s)" . PHP_EOL);

            if(isset($PackageContents["type"]))
            {
                print("Type: " . $PackageContents["type"] . PHP_EOL);
            }
            else
            {
                print("Type: Not available" . PHP_EOL);
            }

            if(isset($PackageContents["ppm_version"]))
            {
                print("PPM Version: " . $PackageContents["ppm_version"] . PHP_EOL);
            }
            else
            {
                print("PPM Version: Not available" . PHP_EOL);
            }

            if(isset($PackageContents["compiler"]))
            {
                print("Compiler Flags: " . json_encode($PackageContents["compiler"], JSON_UNESCAPED_SLASHES) . PHP_EOL);
            }
            else
            {
                print("Compiler Flags: Not available" . PHP_EOL);
            }

            if(isset($PackageContents["package"]))
            {
                print("Package header size: " . strlen(ZiProto::encode($PackageContents["package"])) . " byte(s)" . PHP_EOL);
            }
            else
            {
                print("Package header size: 0 byte(s)" . PHP_EOL);
            }

            if(isset($PackageContents["compiled_components"]))
            {
                print("Compiled components size: " . strlen(ZiProto::encode($PackageContents["compiled_components"])) . " byte(s)" . PHP_EOL);
                print("Compiled components count: " . count($PackageContents["compiled_components"]) . PHP_EOL);
            }
            else
            {
                print("Compiled components size: 0 byte(s)" . PHP_EOL);
                print("Compiled components count: 0" . PHP_EOL);
            }

            if(isset($PackageContents["byte_compiled"]))
            {
                print("Byte compiled size: " . strlen(ZiProto::encode($PackageContents["byte_compiled"])) . " byte(s)" . PHP_EOL);
                print("Byte compiled count: " . count($PackageContents["byte_compiled"]) . PHP_EOL);
            }
            else
            {
                print("Byte compiled size: 0 byte(s)" . PHP_EOL);
                print("Byte compiled count: 0" . PHP_EOL);
            }

            if(isset($PackageContents["raw"]))
            {
                print("Raw (none-compiled components) size: " . strlen(ZiProto::encode($PackageContents["raw"])) . " byte(s)" . PHP_EOL);
                print("Raw (none-compiled components) count: " . count($PackageContents["raw"]) . PHP_EOL);
            }
            else
            {
                print("Raw (none-compiled components) size: 0 byte(s)" . PHP_EOL);
                print("Raw (none-compiled components) count: 0" . PHP_EOL);
            }

            if(isset($PackageContents["files"]))
            {
                print("Files size: " . strlen(ZiProto::encode($PackageContents["files"])) . " byte(s)" . PHP_EOL);
                print("Files count: " . count($PackageContents["files"]) . PHP_EOL);
            }
            else
            {
                print("Files size: 0 byte(s)" . PHP_EOL);
                print("Files count: 0" . PHP_EOL);
            }

            if(isset($PackageContents["post_install"]))
            {
                print("Post install size: " . strlen(ZiProto::encode($PackageContents["post_install"])) . " byte(s)" . PHP_EOL);
            }
            else
            {
                print("Post install size: 0 byte(s)" . PHP_EOL);
            }

            if(isset($PackageContents["pre_install"]))
            {
                print("Pre install size: " . strlen(ZiProto::encode($PackageContents["pre_install"])) . " byte(s)" . PHP_EOL);
            }
            else
            {
                print("Pre install size: 0 byte(s)" . PHP_EOL);
            }

            exit(0);
        }

        /**
         * Simply prints out the package version, this is used for automation purposes
         *
         * @param string $path
         */
        public static function getPackageVersion(string $path)
        {
            try
            {
                $PackageContents = ZiProto::decode(file_get_contents($path));
            }
            catch(Exception $e)
            {
                CLI::logError("The package cannot be opened correctly, the file may corrupted");
                exit(1);
            }

            if(isset($PackageContents['package']) == false)
            {
                CLI::logError("This package is missing information, is this a ppm package?");
                exit(1);
            }

            try
            {
                $PackageInformation = Package::fromArray($PackageContents['package']);
            }
            catch (Exception $e)
            {
                CLI::logError("There was an error while trying to read the package information", $e);
                exit(1);
            }

            print($PackageInformation->Metadata->Version);
            exit(0);
        }

        /**
         * Compiles package from source
         *
         * @param string $path
         * @param string|null $output_directory
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

                $MainFile = file_get_contents($Component->getPath());
            }

            $PackedFiles = array();
            if($Source->Package->Files !== null)
            {
                foreach($Source->Package->Files as $file)
                {
                    $file_path = $Source->Path . DIRECTORY_SEPARATOR . str_ireplace("/", DIRECTORY_SEPARATOR, $file);

                    if(file_exists($file_path) == false)
                    {
                        if(is_link($file_path))
                        {
                            $Source->Package->Files = array_diff($Source->Package->Files, [$file]);
                            CLI::logWarning("Cannot find the file '" . $file_path . "', ignoring since it's likely a broken link");
                        }
                        else
                        {
                            CLI::logError("Cannot find the file '" . $file_path . "'");
                            exit(1);
                        }
                    }
                    else
                    {
                        $PackedFiles[$file] = file_get_contents($file_path);
                    }
                }
            }

            // Pack compiler data (new)
            $CompilerData = array(
                "linting_flag" => self::getLintingFlag(),
                "byte_compiling_flag" => self::getByteCompilingFlag(),
                "compiler_flag" => self::getCompilerFlag(),
            );

            CLI::logEvent("Packing package contents");
            $Contents = array(
                "type" => "ppm_package",
                "ppm_version" => PPM_VERSION,
                "compiler" => $CompilerData,
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