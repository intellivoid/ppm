<?php


    namespace ppm\Objects;


    use ppm\Exceptions\InvalidComponentException;
    use ppm\Exceptions\InvalidConfigurationException;
    use ppm\Exceptions\InvalidDependencyException;
    use ppm\Exceptions\InvalidPackageException;
    use ppm\Exceptions\MissingPackagePropertyException;
    use ppm\Objects\Package\Component;
    use ppm\Objects\Package\Configuration;
    use ppm\Objects\Package\Dependency;
    use ppm\Objects\Package\Metadata;

    /**
     * Class Package
     * @package ppm\Objects
     */
    class Package
    {
        /**
         * @var Metadata
         */
        public $Metadata;

        /**
         * @var Component[]
         */
        public $Components;

        /**
         * @var string[]
         */
        public $Files;

        /**
         * @var Dependency[]
         */
        public $Dependencies;

        /**
         * @var Configuration
         */
        public $Configuration;

        /**
         * Package constructor.
         */
        public function __construct()
        {
            $this->Metadata = new Metadata();
            $this->Components = [];
            $this->Files = [];
            $this->Dependencies = [];
            $this->Configuration = new Configuration();
        }

        /**
         * @return array
         */
        public function toArray(): array
        {
            $component_array = array();

            if($this->Components !== null)
            {
                /** @var Component $component */
                foreach($this->Components as $component)
                {
                    $component_array[] = $component->toArray();
                }
            }

            $dependency_array = array();

            /** @var Dependency $dependency */
            foreach($this->Dependencies as $dependency)
            {
                $dependency_array[] = $dependency->toArray();
            }

            $PackageArray = $this->Metadata->toArray();
            $PackageArray['dependencies'] = $dependency_array;
            $PackageArray['configuration'] = $this->Configuration->toArray();

            $Results = array();
            $Results["package"] = $PackageArray;

            if($this->Components !== null)
            {
                $Results["components"] = $component_array;
            }

            if($this->Files !== null)
            {
                $Results["files"] = $this->Files;
            }

            return $Results;
        }

        /**
         * @param array $data
         * @param string $base_directory
         * @return Package
         * @throws InvalidComponentException
         * @throws InvalidPackageException
         * @throws MissingPackagePropertyException
         * @throws InvalidConfigurationException
         * @throws InvalidDependencyException
         */
        public static function fromArray(array $data, string $base_directory=null): Package
        {
            $PackageObject = new Package();

            if(isset($data['package']))
            {
                $PackageObject->Metadata = Metadata::fromArray($data['package']);
            }
            else
            {
                throw new InvalidPackageException("The package file is missing 'package'");
            }

            if(is_null($base_directory))
            {
                $PackageObject->Components = null;
            }
            else
            {
                if(isset($data['components']))
                {
                    foreach($data['components'] as $component)
                    {
                        $PackageObject->Components[] = Component::fromArray($component, $base_directory);
                    }
                }
                else
                {
                    throw new InvalidPackageException("The package file is missing 'components'");
                }
            }

            if(is_null($base_directory))
            {
                $PackageObject->Files = null;
            }
            else
            {
                if(isset($data["files"]))
                {
                    foreach($data["files"] as $file)
                    {
                        $PackageObject->Files[] = $file;
                    }
                }
                else
                {
                    // Backwards compatibility with 1.0.0.0
                    $PackageObject->Files = [];
                }
            }

            if(isset($data['package']['dependencies']))
            {
                foreach($data['package']['dependencies'] as $dependency)
                {
                    $PackageObject->Dependencies[] = Dependency::fromArray($dependency);
                }
            }
            else
            {
                throw new InvalidPackageException("The package file is missing 'dependencies'");
            }

            if(isset($data['package']['configuration']))
            {
                $PackageObject->Configuration = Configuration::fromArray($data['package']['configuration']);
            }
            else
            {
                throw new InvalidPackageException("The package file is missing 'configuration'");
            }

            return $PackageObject;
        }
    }