<?php


    namespace ppm\Objects;

    use ppm\Abstracts\CompilerFlags;
    use ppm\Exceptions\InvalidComponentException;
    use ppm\Exceptions\InvalidConfigurationException;
    use ppm\Exceptions\InvalidDependencyException;
    use ppm\Exceptions\InvalidPackageException;
    use ppm\Exceptions\MissingPackagePropertyException;
    use ppm\Exceptions\PathNotFoundException;
    use ppm\Objects\Package\Component;
    use ppm\Utilities\CLI;
    use PpmParser\Error;
    use PpmZiProto\ZiProto;

    /**
     * Class Source
     * @package ppm\Objects
     */
    class Source
    {
        /**
         * @var string
         */
        public $Path;

        /**
         * @var Package
         */
        public $Package;

        /**
         * @return string
         */
        public function getPackageConfigurationPath(): string
        {
            return $this->Path . DIRECTORY_SEPARATOR . 'package.json';
        }

        /**
         * Compiles the components and returns an array of compiled assets
         *
         * @param bool $ignoreCompilerErrors
         * @return array
         */
        public function compileComponents(bool $ignoreCompilerErrors=false): array
        {
            $CompiledComponents = array();
            $ByteCompiledComponents = array();
            $RawComponents = array();

            if(CLI\Compiler::getByteCompilingFlag() == CompilerFlags::ByteCompilerError)
            {
                CLI::logWarning("The 'bcerror' flag enabled, components with improper encoding can cause the compiler to fail, normally byte compiling them instead provides as a workaround for this issue");
            }

            /** @var Component $component */
            foreach($this->Package->Components as $component)
            {
                CLI::logVerboseEvent("Compiling " . $component->File . "...", false);

                try
                {
                    $ParsedComponent = $component->parse();
                    $Structure = json_encode($ParsedComponent);

                    if($Structure == false)
                    {
                        CLI::logVerboseEvent("Failed");

                        if(CLI\Compiler::getByteCompilingFlag() == CompilerFlags::ByteCompilerWarning)
                        {
                            CLI::logWarning("Cannot compile " . $component->File . ", " . json_last_error_msg() . ". Will byte-compile instead");
                            CLI::logVerboseEvent("Byte-compiling " . $component->File . "...", false);

                            $ByteCompiledComponents[$component->File] = serialize($ParsedComponent);

                            CLI::logVerboseEvent("Success");
                            CLI::logVerboseEvent("Original: " . strlen(file_get_contents($component->getPath())) . " bytes / Byte compiled: " . strlen($ByteCompiledComponents[$component->File]) . " bytes");
                        }
                        else
                        {
                            CLI::logError("Cannot compile " . $component->File . ", " . json_last_error_msg());
                            exit(1);
                        }
                    }
                    else
                    {
                        $Compiled = ZiProto::encode(json_decode($Structure, true));
                        $CompiledComponents[$component->File] = $Compiled;

                        CLI::logVerboseEvent("Success");
                        CLI::logVerboseEvent("Original: " . strlen(file_get_contents($component->getPath())) . " bytes / Compiled: " . strlen($CompiledComponents[$component->File]) . " bytes");
                    }
                }
                catch(Error $e)
                {
                    if($ignoreCompilerErrors)
                    {
                        CLI::logWarning("Cannot compile " . $component->File . ", " . $e->getMessage());
                        CLI::logWarning($component->File . " will be packed without being compiled");
                        $RawComponents[$component->File] = file_get_contents($component->getPath());
                    }
                    else
                    {
                        CLI::logError("Cannot compile " . $component->File, $e);
                        CLI::logWarning("To ignore these compiler errors, pass on the option '--cwarning', aborting.");
                        exit(1);
                    }
                }
            }

            CLI::logVerboseEvent("Compiled components: " . count($CompiledComponents));
            CLI::logVerboseEvent("Byte compiled components: " . count($ByteCompiledComponents));
            CLI::logVerboseEvent("Raw components: " . count($RawComponents));

            return array(
                "compiled_components" => $CompiledComponents,
                "byte_compiled" => $ByteCompiledComponents,
                "raw" => $RawComponents
            );
        }

        /**
         * @param string $path
         * @return Source
         * @throws InvalidPackageException
         * @throws PathNotFoundException
         * @throws InvalidComponentException
         * @throws InvalidConfigurationException
         * @throws InvalidDependencyException
         * @throws MissingPackagePropertyException
         */
        public static function loadSource(string $path): Source
        {
            $SourceObject = new Source();
            $SourceObject->Path = $path;

            if(file_exists($path) == false)
            {
                throw new PathNotFoundException("The path '$path' was not found");
            }

            if(file_exists($SourceObject->getPackageConfigurationPath()) == false)
            {
                throw new InvalidPackageException("The file 'package.json' was not found, is this a valid ppm package?");
            }

            $PackageConfigurationContents = file_get_contents($SourceObject->getPackageConfigurationPath());
            $SourceObject->Package = Package::fromArray(
                json_decode($PackageConfigurationContents, true),
                $SourceObject->Path
            );

            return $SourceObject;
        }
    }