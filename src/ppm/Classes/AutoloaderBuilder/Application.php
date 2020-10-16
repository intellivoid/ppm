<?php


    namespace ppm\Classes\AutoloaderBuilder;


    use ArrayIterator;
    use ppm\Abstracts\CompilerFlags;
    use ppm\Classes\DirectoryScanner\Exception;
    use ppm\Exceptions\ApplicationException;
    use ppm\Exceptions\CollectorException;
    use ppm\Exceptions\Config;
    use ppm\Utilities\CLI;
    use SplFileInfo;

    /**
     * Class Application
     * @package ppm\Exceptions
     */
    class Application
    {
        private $factory;
        private $config;

        /**
         * Application constructor.
         * @param Config $config
         * @param Factory $factory
         */
        public function __construct(Config $config, Factory $factory)
        {
            $this->config = $config;
            $this->factory = $factory;
        }

        /**
         * @return bool|int
         * @throws ApplicationException
         * @throws Exception|CollectorException
         */
        public function run()
        {
            $result = $this->runCollector();
            if (!$result->hasUnits())
            {
                #throw new ApplicationException('No units were found - process aborted.', ApplicationException::NoUnitsFound);
            }
            if ($result->hasDuplicates())
            {
                return $this->showDuplicatesError($result->getDuplicates());
            }

            if ($this->config->isCacheEnabled())
            {
                $this->factory->getCache()->persist($this->config->getCacheFile());
            }

            $template = @file_get_contents($this->config->getTemplate());
            if ($template === false)
            {
                throw new ApplicationException("Failed to read the template file.");
            }

            $builder = $this->factory->getRenderer($result);
            $code = $builder->render($template);
            if ($this->config->isLintMode())
            {
                $this->runLint($code);
            }
            return $this->runSaver($code, $result);
        }

        /**
         * @return CollectorResult
         * @throws Exception
         * @throws CollectorException
         */
        private function runCollector() 
        {
            if ($this->config->isFollowSymlinks())
            {
                CLI::logEvent('Following symbolic links is enabled.');
            }
            $collector = $this->factory->getCollector();
            foreach ($this->config->getDirectories() as $directory)
            {
                if (is_dir($directory)) {
                    CLI::logEvent('Scanning directory ' . $directory);
                    $scanner = $this->factory->getScanner()->getIterator($directory);
                    $collector->processDirectory($scanner);
                    // this unset is needed to "fix" a segfault on shutdown in some PHP Versions
                    unset($scanner);
                }
                else
                {
                    $file = new SplFileInfo($directory);
                    $filter = $this->factory->getFilter(new ArrayIterator(array($file)));
                    foreach($filter as $file)
                    {
                        CLI::logEvent('Scanning file ' . $file);
                        $collector->processFile($file);
                    }
                }
            }
            return $collector->getResult();
        }

        private function unitStaticRender(CollectorResult $result): string
        {
            $template = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "unit.php.tpl");
            $index = "";

            foreach($result->getUnitFiles() as $unitFile)
            {
                $index .= "require_once \"$unitFile\";\n";
            }

            return  str_ireplace("___UNITLIST___", $index, $template);
        }

        private function runSaver($code, CollectorResult $result)
        {
            $output = $this->config->getOutputFile();
            $unit_output = $output . "_UNITS";
            if (!$this->config->isPharMode())
            {
                if ($output === 'STDOUT')
                {
                    CLI::logEvent("\n");
                    echo $code;
                    CLI::logEvent("\n\n");
                    return 0;
                }

                if(count($result->getUnitFiles()) > 0)
                {
                    $unit_static_code = $this->unitStaticRender($result);
                    $unit_output_written = @file_put_contents($unit_output, $unit_static_code);

                    if ($unit_output_written != strlen($unit_static_code))
                    {
                        CLI::logError("Writing to file '$unit_static_code' failed.");
                        return 1;
                    }

                    CLI::logEvent("Unit file {$unit_output} generated.");
                }

                // @codingStandardsIgnoreStart
                $output_written = @file_put_contents($output, $code);

                // @codingStandardsIgnoreEnd
                if ($output_written != strlen($code))
                {
                    CLI::logError("Writing to file '$output' failed.");
                    return 1;
                }

                CLI::logEvent("Autoload file {$output} generated.");
                return 0;
            }

            if (strpos($code, '__HALT_COMPILER();') === FALSE)
            {
                CLI::logEvent(
                    "Warning: Template used in phar mode did not contain required __HALT_COMPILER() call\n" .
                    "which has been added automatically. The used stub code may not work as intended.\n\n", STDERR);
                $code .= $this->config->getLinebreak() . '__HALT_COMPILER();';
            }

            $pharBuilder = $this->factory->getPharBuilder();

            if ($keyfile = $this->config->getPharKey())
            {
                $pharBuilder->setSignatureKey($this->loadPharSignatureKey($keyfile));
            }

            if ($aliasName = $this->config->getPharAliasName())
            {
                $pharBuilder->setAliasName($aliasName);
            }

            if ($this->config->hasPharHashAlgorithm())
            {
                $pharBuilder->setSignatureType($this->config->getPharHashAlgorithm());
            }

            $pharBuilder->build($output, $code);
            CLI::logEvent("\nphar archive '{$output}' generated.\n\n");
            return 0;
        }

        private function loadPharSignatureKey($keyfile)
        {
            if (!extension_loaded('openssl'))
            {
                throw new ApplicationException('Extension for OpenSSL not loaded - cannot sign phar archive - process aborted.',
                    ApplicationException::OpenSSLError);
            }

            $keydata = file_get_contents($keyfile);

            if (strpos($keydata, 'ENCRYPTED') !== FALSE)
            {
                CLI::logEvent("Passphrase for key '$keyfile': ");
                $g = shell_exec('stty -g');
                shell_exec('stty -echo');
                $passphrase = trim(fgets(STDIN));
                CLI::logEvent("\n");
                shell_exec('stty ' . $g);
                $private = openssl_pkey_get_private($keydata, $passphrase);
            }
            else
            {
                $private = openssl_pkey_get_private($keydata);
            }

            if (!$private)
            {
                throw new ApplicationException("Opening private key '$keyfile' failed - process aborted.\n\n", ApplicationException::OpenSSLError);
            }

            return $private;
        }


        /**
         * Execute a lint check on generated code
         *
         * @param string           $code  Generated code to lint
         *
         * @return boolean
         */
        protected function runLint($code)
        {
            $dsp = array(
                0 => array('pipe', 'r'),
                1 => array('pipe', 'w'),
                2 => array('pipe', 'w')
            );

            $binary = $this->config->getPhp();

            $process = proc_open($binary . ' -l', $dsp, $pipes);

            if (!is_resource($process))
            {
                CLI::logEvent("Opening php binary for linting failed.");
                return 1;
            }

            fwrite($pipes[0], $code);
            fclose($pipes[0]);
            fclose($pipes[1]);

            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            $rc = proc_close($process);

            if ($rc == 255)
            {
                if(CLI\Compiler::getLintingFlag() == CompilerFlags::LintingError)
                {
                    CLI::logError("Syntax errors during lint: " . str_replace('in - on line', 'in generated code on line', $stderr));
                    exit(1);
                }
                else
                {
                    CLI::logWarning("Syntax errors during lint: " . str_replace('in - on line', 'in generated code on line', $stderr));
                }

                return 4;
            }

            CLI::logEvent("Lint check of generated code okay");
            return 0;
        }

        /**
         * @param array $duplicates
         *
         * @return int
         */
        private function showDuplicatesError(array $duplicates)
        {
            CLI::logEvent(sprintf("Multiple declarations of trait(s), interface(s) or class(es). Could not generate autoload map."));

            foreach($duplicates as $unit => $files)
            {
                CLI::logEvent(sprintf("Unit '%s' defined in: " . $unit));

                /** @var array $files */
                foreach($files as $file)
                {
                    CLI::logEvent(sprintf(" - %s\n", $file));
                }
            }

            return 0;
        }

    }