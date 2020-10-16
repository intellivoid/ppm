<?php


    namespace ppm\Utilities;


    use InvalidArgumentException;
    use ppm\Abstracts\AutoloadMethod;
    use ppm\Objects\Package;
    use ppm\Utilities\CLI\Compiler;
    use ppm\Utilities\CLI\Tools;

    /**
     * Class Compatibility
     * @package ppm\Utilities
     */
    class Compatibility
    {
        /**
         * Strips unwanted characters from the version string
         *
         * @param string $version
         * @return string
         */
        public static function convertVersion(string $version): string
        {
            return str_ireplace("v", "", $version);
        }

        /**
         * Converts a composer package name to a PPM package name
         *
         * @param string $input
         * @param string $domain
         * @return string
         */
        public static function composerPackageToPpm(string $input, string $domain="com"): string
        {
            $parsed_input = explode("/", $input);
            if(count($parsed_input) == 2)
            {
                return str_ireplace("-", "_", $domain . "." . $parsed_input[1] . "." . $parsed_input[0]);
            }

            throw new InvalidArgumentException("The input isn't valid");
        }

        /**
         * This method generates a PPM package from a composer lock entry and will return the
         * path of the source directory containing package.json
         *
         * @param string $path
         * @param array $data
         * @param bool $discover_from_vendor
         * @return string
         */
        public static function generatePackageFromComposerLockEntry(string $path, array $data, bool $discover_from_vendor=false): string
        {
            $Package = new Package();

            CLI::logEvent("Parsing metadata of '" . $data["name"] . "'");
            if(isset($data["source"]))
            {
                if(isset($data["source"]["url"]))
                {
                    $Package->Metadata->URL = $data["source"]["url"];
                    CLI::logVerboseEvent("Metadata source.url='" . $Package->Metadata->URL . "'");
                }
            }

            if(isset($data["name"]))
            {
                $Package->Metadata->PackageName = Compatibility::composerPackageToPpm($data["name"]);
                $Package->Metadata->Name = $data["name"];

                CLI::logVerboseEvent("Metadata name='" . $Package->Metadata->Name . "'");
                CLI::logVerboseEvent("Compatibility name='" . $Package->Metadata->PackageName . "'");
            }

            if(isset($data["description"]))
            {
                $Package->Metadata->Description = $data["description"];
            }

            if(isset($data["authors"]))
            {
                if(count($data["authors"]) > 0)
                {
                    if(isset($data["authors"][0]["name"]))
                    {
                        if(isset($data["authors"][0]["email"]))
                        {
                            $Package->Metadata->Author = $data["authors"][0]["name"] . " <" . $data["authors"][0]["email"] . ">";
                            CLI::logVerboseEvent("Metadata authors.0.name='" . $data["authors"][0]["name"] . "'");
                            CLI::logVerboseEvent("Metadata authors.0.email='" . $data["authors"][0]["email"] . "'");

                        }
                        else
                        {
                            $Package->Metadata->Author = $data["authors"][0]["name"];
                            CLI::logVerboseEvent("Metadata authors.0.name='" . $data["authors"][0]["name"] . "'");
                        }
                    }
                }
            }

            if(isset($data["version"]))
            {
                $Package->Metadata->Version = Compatibility::convertVersion($data["version"]);
                CLI::logVerboseEvent("Metadata version='" . $data["version"] . "'");
                CLI::logVerboseEvent("Compatibility version='" . $Package->Metadata->Version . "'");
            }

            CLI::logEvent("Validating package");
            Compiler::validatePackage($Package);

            if(isset($data["bin"]))
            {
                if(count($data["bin"]) > 0)
                {
                    $vendor_path = $path . DIRECTORY_SEPARATOR . "vendor";
                    $package_bin_path = str_ireplace("/", DIRECTORY_SEPARATOR, $data["name"]) . DIRECTORY_SEPARATOR . str_ireplace("/", DIRECTORY_SEPARATOR, $data["bin"][0]);


                    if(file_exists($vendor_path . DIRECTORY_SEPARATOR . $package_bin_path) == false)
                    {
                        CLI::logWarning("The specified bin file '$package_bin_path' cannot be found in '$package_bin_path', the 'main' configuration will not be configured");
                    }
                    else
                    {
                        CLI::logEvent("Generating main configuration for '" . $package_bin_path . "'");
                        CLI::logWarning("For security and compatibility purposes, no symlink will be created for '$package_bin_path'");

                        if(count($data["bin"]) > 1)
                        {
                            CLI::logWarning("PPM Only supports one main execution point per package, so only the first configuration will be generated");
                        }

                        $Package->Configuration->Main = new Package\Configuration\MainExecution();
                        $Package->Configuration->Main->Name = basename($package_bin_path);
                        $Package->Configuration->Main->ExecutionPoint = $package_bin_path;
                        $Package->Configuration->Main->CreateSymlink = false;
                    }
                }

            }

            if($discover_from_vendor)
            {
                $autoloader_path = $path . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

                // If the package components are discovered from vendor, assume
                if(file_exists($autoloader_path))
                {
                    CLI::logEvent("Will use composers pre-generated autoloader");
                    $Package->Configuration->AutoLoadMethod = AutoloadMethod::ComposerGenerator;
                }
                else
                {
                    CLI::logError("The required file path '$autoloader_path' is missing");
                    exit(1);
                }
            }
            else
            {
                CLI::logEvent("Parsing dependencies of '" . $data["name"] . "'");
                // TODO: Respect the autoloading method that's defined in the lock fie
                $Package->Configuration->AutoLoadMethod = AutoloadMethod::StandardPhpLibrary;

                // Parse the dependencies
                if(isset($data["require"]))
                {
                    foreach($data["require"] as $name => $version)
                    {
                        if(stripos($name, "ext-") == false)
                        {
                            $Dependency = new Package\Dependency();

                            try
                            {
                                $Dependency->Package = Compatibility::composerPackageToPpm($name);
                                $Dependency->Version = "latest"; // TODO: Add better version scaling method
                                $Dependency->Required = true;

                                CLI::logVerboseEvent("Adding dependency '" . $name . "' (required)");
                                $Package->Dependencies[] = $Dependency;
                            }
                            catch(InvalidArgumentException $e)
                            {
                                CLI::logVerboseEvent("Skipping dependency '" . $name . "'");
                                unset($e);
                            }
                        }
                        else
                        {
                            CLI::logVerboseEvent("Skipping dependency '" . $name . "'");
                        }
                    }
                }

                // Parse the optional dependencies (dev)
                if(isset($data["require-dev"]))
                {
                    foreach($data["require-dev"] as $name => $version)
                    {
                        if(stripos($name, "ext-") == false)
                        {
                            $Dependency = new Package\Dependency();

                            try
                            {
                                $Dependency->Package = Compatibility::composerPackageToPpm($name);
                                $Dependency->Version = "latest"; // TODO: Add better version scaling method
                                $Dependency->Required = false;

                                CLI::logVerboseEvent("Adding dependency '" . $name . "' (not-required)");
                                $Package->Dependencies[] = $Dependency;
                            }
                            catch(InvalidArgumentException $e)
                            {
                                CLI::logVerboseEvent("Skipping dependency '" . $name . "'");
                                unset($e);
                            }
                        }
                        else
                        {
                            CLI::logVerboseEvent("Skipping dependency '" . $name . "'");
                        }
                    }
                }
            }

            // Discover components
            if($discover_from_vendor)
            {
                $source_path = $path . DIRECTORY_SEPARATOR . "vendor";
                CLI::logVerboseEvent("Discovering files from vendor '" . $source_path . "'");
            }
            else
            {
                $source_path = $path . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . str_ireplace("/", DIRECTORY_SEPARATOR, $data["name"]);
                CLI::logVerboseEvent("Discovering files from source '" . $source_path . "'");
            }

            CLI::logEvent("Discovering components");
            $Package->Components = [];
            foreach(Tools::discoverComponents($source_path) as $file)
            {
                $Component = new Package\Component();
                $Component->File = $file;
                $Component->BaseDirectory = $source_path;
                $Component->Required = true;
                $Package->Components[] = $Component;
            }

            CLI::logEvent("Discovering files");
            $Package->Files = [];
            foreach(Tools::discoverFiles($source_path) as $file)
            {
                CLI::logVerboseEvent("Found " . $file);
                $Package->Files[] = $file;
            }

            CLI::logEvent("Generating package.json");
            CLI::logVerboseEvent("package.json path '" . $source_path . "'");

            $results = json_encode($Package->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            file_put_contents($source_path . DIRECTORY_SEPARATOR . "package.json", $results);

            return $source_path;
        }
    }