<?php


    namespace ppm\Utilities\CLI;

    use ppm\Exceptions\InvalidComponentException;
    use ppm\Exceptions\InvalidConfigurationException;
    use ppm\Exceptions\InvalidDependencyException;
    use ppm\Exceptions\InvalidPackageException;
    use ppm\Exceptions\MissingPackagePropertyException;
    use ppm\Objects\Package;
    use ppm\Utilities\CLI;
    use RecursiveDirectoryIterator;
    use RecursiveIteratorIterator;

    /**
     * Class Tools
     * @package ppm\Utilities\CLI
     */
    class Tools
    {
        /**
         * Generates/Updates a package.json file
         *
         * @param string $path
         * @param bool $recreate If True, then the existing package.json file will be recreated from scratch
         */
        public static function generatePackageJson(string $path, bool $recreate=false)
        {
            $file_path = $path . DIRECTORY_SEPARATOR . "package.json";
            $Package = new Package();

            if(file_exists($file_path))
            {
                $PackageConfigurationContents = file_get_contents($file_path);

                try
                {
                    $Package = Package::fromArray(json_decode($PackageConfigurationContents, true), $path);
                }
                catch (InvalidComponentException $e)
                {
                    if($recreate == false)
                    {
                        CLI::logError("The existing package.json file contains invalid/missing components, correct the error or pass the --recreate parameter", $e);
                        exit(255);
                    }
                }
                catch (InvalidConfigurationException $e)
                {
                    if($recreate == false)
                    {
                        CLI::logError("The existing package.json file contains a invalid/missing configuration, correct the error or pass the --recreate parameter", $e);
                        exit(255);
                    }
                }
                catch (InvalidDependencyException $e)
                {
                    if($recreate == false)
                    {
                        CLI::logError("The existing package.json file contains invalid/missing dependencies, correct the error or pass the --recreate parameter", $e);
                        exit(255);
                    }
                }
                catch (InvalidPackageException $e)
                {
                    if($recreate == false)
                    {
                        CLI::logError("The existing package.json file contains invalid/missing package information, correct the error or pass the --recreate parameter", $e);
                        exit(255);
                    }
                }
                catch (MissingPackagePropertyException $e)
                {
                    if($recreate == false)
                    {
                        CLI::logError("The existing package.json file is missing essential properties, correct the error or pass the --recreate parameter", $e);
                        exit(255);
                    }
                }
            }

            // Check package name
            if($Package->Metadata->PackageName !== null)
            {
                if($recreate)
                {
                    $Package->Metadata->PackageName = CLI::getParameter("package-name", "Package Name (com.organization.package_name)", false);
                }
            }
            else
            {
                $Package->Metadata->PackageName = CLI::getParameter("package-name", "Package Name (com.organization.package_name)", false);
            }

            // Check name
            if($Package->Metadata->Name !== null)
            {
                if($recreate)
                {
                    $Package->Metadata->Name = CLI::getParameter("name", "Name (ExampleLibrary)", false);
                }
            }
            else
            {
                $Package->Metadata->Name = CLI::getParameter("name", "Name (ExampleLibrary)", false);
            }

            // Check description
            if($Package->Metadata->Description !== null)
            {
                if($recreate)
                {
                    $Package->Metadata->Description = CLI::getParameter("description", "Description", false);
                }
            }
            else
            {
                $Package->Metadata->Description = CLI::getParameter("description", "Description", false);
            }

            // Check author
            if($Package->Metadata->Author !== null)
            {
                if($recreate)
                {
                    $Package->Metadata->Author = CLI::getParameter("author", "Author", false);
                }
            }
            else
            {
                $Package->Metadata->Author = CLI::getParameter("author", "Author", false);
            }


            // Check organization
            if($Package->Metadata->Organization !== null)
            {
                if($recreate)
                {
                    $Package->Metadata->Organization = CLI::getParameter("organization", "Organization", false);
                }
            }
            else
            {
                $Package->Metadata->Organization = CLI::getParameter("organization", "Organization", false);
            }

            // Check URL
            if($Package->Metadata->URL !== null)
            {
                if($recreate)
                {
                    $Package->Metadata->URL = CLI::getParameter("url", "URL (Leave empty if none)", false);
                }
            }
            else
            {
                $Package->Metadata->URL = CLI::getParameter("url", "URL (Leave empty if none)", false);
            }

            if($Package->Metadata->Version !== null)
            {
                if($recreate)
                {
                    $Package->Metadata->Version = CLI::getParameter("version", "Version (Major.Minor.Build.Revision)", false);
                }
            }
            else
            {
                $Package->Metadata->Version = CLI::getParameter("version", "Version (Major.Minor.Build.Revision)", false);
            }

            CLI::logEvent("Validating package");
            Compiler::validatePackage($Package);
            $Package->Configuration->AutoLoadMethod = "indexed";

            CLI::logEvent("Discovering components");
            $Package->Components = [];
            foreach(self::discoverComponents($path) as $file)
            {
                $Component = new Package\Component();
                $Component->File = $file;
                $Component->BaseDirectory = $path;
                $Component->Required = true;

                $Package->Components[] = $Component;
            }

            CLI::logEvent("Discovering files");
            $Package->Files = [];
            foreach(self::discoverFiles($path) as $file)
            {
                $Package->Files[] = $file;
            }

            CLI::logEvent("Generating package.json");
            $results = json_encode($Package->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            file_put_contents($path . DIRECTORY_SEPARATOR . "package.json", $results);
            CLI::logEvent("Completed");

            exit(0);
        }

        /**
         * Discovers all PHP components
         *
         * @param string $path
         * @return array
         */
        public static function discoverComponents(string $path): array
        {
            $di = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
            $it = new RecursiveIteratorIterator($di);
            $results = [];

            foreach($it as $file)
            {
                if (pathinfo($file, PATHINFO_EXTENSION) == "php")
                {
                    $results[] = str_ireplace($path . DIRECTORY_SEPARATOR, "", $file);
                }
            }

            return $results;
        }

        /**
         * Discovers all ordinary files
         *
         * @param string $path
         * @return array
         */
        public static function discoverFiles(string $path): array
        {
            $di = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
            $it = new RecursiveIteratorIterator($di);
            $results = [];

            foreach($it as $file)
            {
                if (pathinfo($file, PATHINFO_EXTENSION) !== "php")
                {
                    if(is_dir($file) == false)
                    {
                        $results[] = str_ireplace($path . DIRECTORY_SEPARATOR, "", $file);
                    }
                }
            }

            return $results;
        }
    }