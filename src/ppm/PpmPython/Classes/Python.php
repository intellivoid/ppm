<?php


    namespace PpmPython\Classes;

    use Exception;
    use ppm\Utilities\CLI;
    use PpmPython\Exceptions\ParserException;
    use PpmPython\Exceptions\PipException;
    use PpmPython\Exceptions\PythonException;
    use PpmPython\Objects\InstalledPackage;
    use PpmPython\Objects\PipInstall;
    use PpmPython\Objects\PythonInstall;
    use PpmPython\Utilities\Parse;
    use PpmPython\Utilities\Process;

    /**
     * Class Python
     * @package PpmPython\Classes
     */
    class Python
    {
        /**
         * Location of system/shared binaries
         *
         * @var string[]
         */
        private static $BinLocations = ["/bin", "/usr/bin"];

        /**
         * Detects the installed versions of python
         *
         * @return PythonInstall[]
         */
        public static function detectInstalledVersions(): array
        {
            $possible_targets = [];

            // Scan the binary locations for Python installations
            foreach(Python::$BinLocations as $binLocation)
            {
                foreach(scandir($binLocation) as $bin)
                    if(stripos(strtolower($bin), "python") === 0)
                        $possible_targets[] = $binLocation . DIRECTORY_SEPARATOR . $bin;
            }

            // Verify the installs
            $python_installs = [];
            $python_versions = [];

            foreach($possible_targets as $possible_target)
            {
                CLI::logVerboseEvent("Processing '$possible_target'");
                if(is_executable($possible_target) == false)
                    continue;

                $pythonInstall = new PythonInstall();
                $pythonInstall->Path = $possible_target;

                // Determine the version, ignore execution errors
                try
                {
                    $pythonInstall->Version = self::detectVersion($possible_target);
                    CLI::logVerboseEvent("Version detected: " . $pythonInstall->Version);
                }
                catch (PythonException $e)
                {
                    CLI::logVerboseEvent("Cannot detect version, " . $e->getMessage());
                    continue;
                }

                if(in_array($pythonInstall->Version, $python_versions))
                {
                    CLI::logVerboseEvent("Skipping since this version was already found");
                    continue;
                }

                // Determine the rest of the information about this install
                try
                {
                    CLI::logVerboseEvent("Detecting pip Version");
                    $pythonInstall->PipInstall = self::detectPipVersion($possible_target);
                    CLI::logVerboseEvent("pip Version: " . $pythonInstall->PipInstall->Version);
                    CLI::logVerboseEvent("pip Install: " . $pythonInstall->PipInstall->InstallPath);

                    CLI::logVerboseEvent("Finding shared library path");
                    $pythonInstall->SharedLibraryPath = self::findSharedLibraryPath($possible_target);
                    CLI::logVerboseEvent("Shared Library Path: " . $pythonInstall->SharedLibraryPath);

                    CLI::logVerboseEvent("Finding site packages paths");
                    $pythonInstall->SitePackagesPaths = self::findSitePackagesPaths($possible_target);
                    CLI::logVerboseEvent("Site packages paths: " . json_encode($pythonInstall->SitePackagesPaths, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                }
                catch (PythonException | PipException $e)
                {
                    CLI::logWarning($e->getMessage());
                    continue;
                }

                // Search for venv if it's installed/supported
                CLI::logVerboseEvent("Finding venv install");
                if(file_exists($pythonInstall->SharedLibraryPath . DIRECTORY_SEPARATOR . "venv") == false)
                {
                    $venv_found = false;

                    foreach($pythonInstall->SitePackagesPaths as $sitePackagesPath)
                    {
                        if(file_exists($sitePackagesPath . DIRECTORY_SEPARATOR . "venv"))
                        {
                            CLI::logVerboseEvent("venv install: " . $sitePackagesPath . DIRECTORY_SEPARATOR . "venv");
                            $venv_found = true;
                        }
                    }

                    if ($venv_found == false)
                    {
                        CLI::logWarning("Python (" . $pythonInstall->Version . ") cannot be used by PPM because venv is not installed or supported.");
                        continue;
                    }
                }
                else
                {
                    CLI::logVerboseEvent("venv install: " . $pythonInstall->SharedLibraryPath . DIRECTORY_SEPARATOR . "venv");
                }

                // Find installed packages
                try
                {
                    CLI::logVerboseEvent("Detecting installed packages");
                    $pythonInstall->InstalledPackages = self::fetchInstalledPackages($possible_target);
                    CLI::logVerboseEvent("Found " . count($pythonInstall->InstalledPackages) . " item(s)");
                }
                catch (PythonException $e)
                {
                    CLI::logWarning("There was an error while trying to find the installed packages for Python (" . $pythonInstall->Version . ")," . $e->getMessage());
                    continue;
                }

                $python_versions[] = $pythonInstall->Version;
                $python_installs[] = $pythonInstall;

                CLI::logEvent("Found Python " . $pythonInstall->Version);
            }

            return $python_installs;
        }

        /**
         * Determines the shared library path by importing site and running getsitepackages()
         *
         * @param string $path
         * @return string[]
         * @throws PythonException
         */
        private static function findSitePackagesPaths(string $path): array
        {
            try
            {
                $proc = new Process($path . " -c \"import site; print(site.getsitepackages())\"");
                if ($proc->execute() == false)
                    throw new PythonException("Cannot execute '$path', " . $proc->getError(), 0);
                $stdout = $proc->getOutput();
            }
            catch(Exception $e)
            {
                throw new PythonException("Cannot execute '$path', " . $e->getMessage(), 0, $e);
            }

            $stdout = str_ireplace("\n", "", $stdout);
            $stdout = str_ireplace("\r", "", $stdout);
            $stdout = str_ireplace("'", "\"", $stdout);

            $decoded_paths = json_decode($stdout, true);
            if($decoded_paths == false)
                throw new PythonException("Cannot determine site packages paths for '$path', results aren't understood");

            return $decoded_paths;
        }

        /**
         * Determines the shared library path by importing site and finding the file location of the module
         *
         * @param string $path
         * @return string
         * @throws PythonException
         */
        private static function findSharedLibraryPath(string $path): string
        {
            try
            {
                $proc = new Process($path . " -c \"import site as _; print(_.__file__)\"");
                if ($proc->execute() == false)
                    throw new PythonException("Cannot execute '$path', " . $proc->getError(), 0);
                $stdout = $proc->getOutput();
            }
            catch(Exception $e)
            {
                throw new PythonException("Cannot execute '$path', " . $e->getMessage(), 0, $e);
            }

            $stdout = str_ireplace("\n", "", $stdout);
            $stdout = str_ireplace("\r", "", $stdout);

            if(file_exists($stdout))
                return dirname($stdout);

            throw new PythonException("Cannot determine shared library path for '$path', found '$stdout' (which doesn't exist as a file path)");
        }

        /**
         * Detects the version of the python executable
         *
         * @param string $path
         * @return string
         * @throws PythonException
         */
        private static function detectVersion(string $path): string
        {
            try
            {
                $proc = new Process($path . " -V");
                if ($proc->execute() == false)
                    throw new PythonException("Cannot execute '$path', " . $proc->getError(), 0);
                $stdout = $proc->getOutput();
            }
            catch(Exception $e)
            {
                throw new PythonException("Cannot execute '$path', " . $e->getMessage(), 0, $e);
            }

            try
            {
                return Parse::pythonVersionString($stdout);
            }
            catch(ParserException $parserException)
            {
                throw new PythonException("Cannot detect version for '$path', " . $parserException->getMessage(), 0, $parserException);
            }
        }

        /**
         * Determines if Pip is installed and if so, determine the version
         *
         * @param string $path
         * @return PipInstall
         * @throws PipException
         * @throws PythonException
         */
        private static function detectPipVersion(string $path): PipInstall
        {
            try
            {
                $proc = new Process($path . " -m pip -V");
                if ($proc->execute() == false)
                    throw new PythonException("Cannot execute '$path', " . $proc->getError(), 0);
                $stdout = $proc->getOutput();
            }
            catch(Exception $e)
            {
                throw new PythonException("Cannot execute '$path', " . $e->getMessage(), 0, $e);
            }

            try
            {
                return Parse::pipVersionString($stdout);
            }
            catch(ParserException $parserException)
            {
                throw new PipException("Cannot detect version for pip at '$path', verify that it's installed", 0, $parserException);
            }
        }

        /**
         * Fetches all the installed packages from pip
         *
         * @param string $path
         * @return InstalledPackage[]
         * @throws PythonException
         */
        private static function fetchInstalledPackages(string $path): array
        {
            $results = [];

            try
            {
                $proc = new Process($path . " -m pip list --format json --verbose");
                if ($proc->execute() == false)
                    throw new PythonException("Cannot execute '$path', " . $proc->getError(), 0);
                $stdout = $proc->getOutput();
            }
            catch(Exception $e)
            {
                throw new PythonException("Cannot execute '$path', " . $e->getMessage(), 0, $e);
            }

            $stdout = str_ireplace("\n", "", $stdout);
            $stdout = str_ireplace("\r", "", $stdout);

            $decoded = json_decode($stdout, true);
            foreach($decoded as $item)
                $results[] = InstalledPackage::fromArray($item);

            return $results;
        }

    }