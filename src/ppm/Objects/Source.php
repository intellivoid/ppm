<?php


    namespace ppm\Objects;

    use ppm\Exceptions\InvalidComponentException;
    use ppm\Exceptions\InvalidConfigurationException;
    use ppm\Exceptions\InvalidDependencyException;
    use ppm\Exceptions\InvalidPackageException;
    use ppm\Exceptions\MissingPackagePropertyException;
    use ppm\Exceptions\PathNotFoundException;
    use ppm\Objects\Package\Component;
    use ppm\Utilities\CLI;
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
         * @param bool $stdout
         * @return array
         */
        public function compileComponents(bool $stdout=False): array
        {
            $CompiledComponents = array();
            $ByteCompiledComponents = array();

            /** @var Component $component */
            foreach($this->Package->Components as $component)
            {
                if($stdout)
                {
                    CLI::logEvent("Compiling " . $component->File . "...", false);
                }

                $ParsedComponent = $component->parse();
                $Structure = json_encode($ParsedComponent);

                if($Structure == false)
                {
                    if($stdout)
                    {
                        CLI::logEvent("Failed");
                        CLI::logWarning("Cannot compile " . $component->File . ", " . json_last_error_msg() . ". Will byte-compile instead");
                        CLI::logEvent("Byte-compiling " . $component->File . "...", false);
                    }

                    $ByteCompiledComponents[$component->File] = serialize($ParsedComponent);

                    if($stdout)
                    {
                        CLI::logEvent("Success");
                    }
                }
                else
                {
                    $Compiled = ZiProto::encode(json_decode($Structure, true));
                    $CompiledComponents[$component->File] = $Compiled;

                    if($stdout)
                    {
                        CLI::logEvent("Success");
                    }
                }
            }

            return array(
                "compiled_components" => $CompiledComponents,
                "byte_compiled" => $ByteCompiledComponents
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