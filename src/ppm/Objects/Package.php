<?php


    namespace ppm\Objects;


    use ppm\Exceptions\InvalidComponentException;
    use ppm\Exceptions\InvalidPackageException;
    use ppm\Exceptions\MissingPackagePropertyException;
    use ppm\Objects\Package\Component;
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
         * @var array|Component
         */
        public $Components;

        /**
         * @var array|Dependency
         */
        public $Dependencies;

        /**
         * Package constructor.
         */
        public function __construct()
        {
            $this->Components = [];
            $this->Dependencies = [];
        }

        /**
         * @return array
         */
        public function toArray(): array
        {
            $component_array = array();

            /** @var Component $component */
            foreach($this->Components as $component)
            {
                $component_array[] = $component->toArray();
            }

            $dependency_array = array();

            /** @var Dependency $dependency */
            foreach($this->Dependencies as $dependency)
            {
                $dependency_array[] = $dependency->toArray();
            }

            return array(
                'package' => $this->Metadata->toArray(),
                'dependencies' => $dependency_array,
                'components' => $component_array
            );
        }

        /**
         * @param array $data
         * @param string $base_directory
         * @return Package
         * @throws InvalidPackageException
         * @throws InvalidComponentException
         * @throws MissingPackagePropertyException
         */
        public static function fromArray(array $data, string $base_directory): Package
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

            if(isset($data['dependencies']))
            {
                foreach($data['dependencies'] as $dependency)
                {
                    $PackageObject->Dependencies[] = Dependency::fromArray($dependency);
                }
            }
            else
            {
                throw new InvalidPackageException("The package file is missing 'dependencies'");
            }

            return $PackageObject;
        }
    }