<?php


    namespace ppm\Utilities\CLI;

    use Exception;
    use ppm\Classes\Composer\Wrapper;
    use ppm\Exceptions\InvalidComponentException;
    use ppm\Exceptions\InvalidConfigurationException;
    use ppm\Exceptions\InvalidDependencyException;
    use ppm\Exceptions\InvalidPackageException;
    use ppm\Exceptions\MissingPackagePropertyException;
    use ppm\Objects\Package;
    use ppm\Objects\Source;
    use ppm\Utilities\CLI;
    use ppm\Utilities\IO;
    use ppm\Utilities\PathFinder;
    use ppm\Utilities\System;
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
                        exit(1);
                    }
                }
                catch (InvalidConfigurationException $e)
                {
                    if($recreate == false)
                    {
                        CLI::logError("The existing package.json file contains a invalid/missing configuration, correct the error or pass the --recreate parameter", $e);
                        exit(1);
                    }
                }
                catch (InvalidDependencyException $e)
                {
                    if($recreate == false)
                    {
                        CLI::logError("The existing package.json file contains invalid/missing dependencies, correct the error or pass the --recreate parameter", $e);
                        exit(1);
                    }
                }
                catch (InvalidPackageException $e)
                {
                    if($recreate == false)
                    {
                        CLI::logError("The existing package.json file contains invalid/missing package information, correct the error or pass the --recreate parameter", $e);
                        exit(1);
                    }
                }
                catch (MissingPackagePropertyException $e)
                {
                    if($recreate == false)
                    {
                        CLI::logError("The existing package.json file is missing essential properties, correct the error or pass the --recreate parameter", $e);
                        exit(1);
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
            if($Package->Configuration->AutoLoadMethod == null)
                $Package->Configuration->AutoLoadMethod = "generated_spl";

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
                CLI::logVerboseEvent("Found " . $file);
                $Package->Files[] = $file;
            }

            CLI::logEvent("Generating package.json");
            $results = json_encode($Package->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            file_put_contents($path . DIRECTORY_SEPARATOR . "package.json", $results);
            CLI::logEvent("Completed");

            exit(0);
        }

        /**
         * Generates an Continues Integration script for the PPM package
         *
         * @param string $path
         */
        public static function generateCi(string $path)
        {
            CLI::logEvent("Loading from source");

            try
            {
                $source_path = Compiler::findSource($path);
                CLI::logVerboseEvent("Source path => " . $source_path);
                $source = Source::loadSource($source_path);
            }
            catch (Exception $e)
            {
                CLI::logError("There was an error while trying to load from source", $e);
                exit(1);
            }

            CLI::logEvent("Validating package");
            Compiler::validatePackage($source->Package);

            CLI::logEvent("Creating CI files for " . $source->Package->Metadata->PackageName);

            /**
             * GITHUB WORKFLOW
             */
            CLI::logEvent("Generating GitHub Workflow");
            CLI::logWarning("You must include 'PPM_ACCESS_TOKEN' in your repositories secrets with proper read access to intellivoid/ppm");

            $GitHubWorkflow = self::applyBuildTemplate(__DIR__ . DIRECTORY_SEPARATOR . "Templates" . DIRECTORY_SEPARATOR . "github_ci.yml", $path, $source);
            self::smartMkdir($path . DIRECTORY_SEPARATOR . ".github");
            self::smartMkdir($path . DIRECTORY_SEPARATOR . ".github" . DIRECTORY_SEPARATOR . "workflows");
            self::smartWrite(
                $path . DIRECTORY_SEPARATOR . ".github" . DIRECTORY_SEPARATOR . "workflows" .
                DIRECTORY_SEPARATOR . $source->Package->Metadata->PackageName . ".ppm.yml", $GitHubWorkflow);

            CLI::logEvent("Completed");
            exit(0);
        }

        /**
         * Writes to a file (overwrites it if it exists)
         *
         * @param string $path
         * @param $content
         */
        private static function smartWrite(string $path, $content)
        {
            if(file_exists($path))
                unlink($path);

            file_put_contents($path, $content);
        }

        /**
         * Creates a directory in a safe way
         *
         * @param string $path
         * @return bool
         */
        private static function smartMkdir(string $path): bool
        {
            if(file_exists($path))
            {
                if(is_dir($path))
                    return True;

                CLI::logError("'$path' Already exists and it is not a directory");
                exit(1);
            }

            mkdir($path);
            return True;
        }

        /**
         * Applies a build template
         *
         * @param string $file
         * @param Source $source
         * @return string
         */
        private static function applyBuildTemplate(string $file, string $path, Source $source): string
        {
            $file_contents = file_get_contents($file);
            if(isset(CLI::options()["runtime-version"]))
            {
                $file_contents = str_ireplace("%RUNTIME_VERSION%", CLI::options()["runtime-version"], $file_contents);
            }
            else
            {
                $file_contents = str_ireplace("%RUNTIME_VERSION%", "8.0", $file_contents);
            }

            if(isset(CLI::options()["branch"]))
            {
                $file_contents = str_ireplace("%BRANCH%", CLI::options()["branch"], $file_contents);
            }
            else
            {
                $file_contents = str_ireplace("%BRANCH%", "master", $file_contents);
            }

            $file_contents = str_ireplace("%SRC%", str_ireplace($path . DIRECTORY_SEPARATOR, "", $source->Path), $file_contents);
            $file_contents = str_ireplace("%PACKAGE_NAME%", $source->Package->Metadata->PackageName, $file_contents);
            $file_contents = str_ireplace("%PACKAGE_NAME_SAFE%", $source->Package->Metadata->Name, $file_contents);
            $file_contents = str_ireplace("%STATE%", PPM_STATE, $file_contents);

            return $file_contents;
        }

        /**
         * Discovers all PHP components
         *
         * @param string $path
         * @return array
         */
        public static function discoverComponents(string $path): array
        {
            CLI::logVerboseEvent("Discovering components started");
            $di = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
            $it = new RecursiveIteratorIterator($di);
            $results = [];

            foreach($it as $file)
            {
                if (pathinfo($file, PATHINFO_EXTENSION) == "php")
                {
                    CLI::logVerboseEvent("Found " . $file);
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
            CLI::logVerboseEvent("Discovering files started");
            $di = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
            $it = new RecursiveIteratorIterator($di);
            $results = [];

            foreach($it as $file)
            {
                if (pathinfo($file, PATHINFO_EXTENSION) !== "php")
                {
                    if(is_dir($file) == false)
                    {
                        CLI::logVerboseEvent("Found " . $file);
                        $results[] = str_ireplace($path . DIRECTORY_SEPARATOR, "", $file);
                    }
                }
            }

            return $results;
        }

        /**
         * Clears the cache folder for PPM
         */
        public static function clearCache()
        {
            if(System::isRoot() == false)
            {
                CLI::logError("This operation requires root privileges, please run ppm with 'sudo -H'");
                exit(1);
            }

            if(IO::writeTest(PathFinder::getMainPath(true)) == false)
            {
                CLI::logError("Write test failed, cannot write to the PPM installation directory");
                exit(1);
            }

            CLI::logEvent("Clearing indexed cache");
            IO::deleteDirectory(PathFinder::getCachePath());
            PathFinder::getCachePath(true);

            CLI::logEvent("Clearing build cache");
            IO::deleteDirectory(PathFinder::getBuildPath(false));

            CLI::logEvent("Clearing repo cache");
            IO::deleteDirectory(PathFinder::getRemoteRepoPath(false));

            CLI::logEvent("Clearing composer cache");
            IO::deleteDirectory(PathFinder::getComposerTemporaryPath(false));

            $wrapper = Wrapper::create(__DIR__);
            $arguments = array(
                "clear-cache", // Clear the cache
                "--no-interaction" // No interaction
            );

            // Construct additional arguments
            if(CLI::$VerboseMode)
            {
                $arguments[] = "--verbose";
            }

            // Execute the clear cache process for composer
            CLI::logVerboseEvent("Executing " . implode(" ", $arguments));

            CLI::logEvent("Running composer");
            $exit_code = $wrapper->run(implode(" ", $arguments));
            CLI::logVerboseEvent("Composer exit code '$exit_code'");

            CLI::logEvent("Success");
        }
    }