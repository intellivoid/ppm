<?php


    namespace ppm\Objects\Package;

    use ppm\Exceptions\InvalidDependencyException;

    /**
     * Class Dependency
     * @package ppm\Objects\Package
     */
    class Dependency
    {
        /**
         * @var string
         */
        public $Package;

        /**
         * @var string
         */
        public $Version;

        /**
         * @var bool
         */
        public $Required;

        /**
         * Optional source install that tells PPM where and how to install the package
         *
         * @var string|null
         */
        public $Source;

        /**
         * @return array
         */
        public function toArray(): array
        {
            return array(
                "package" => $this->Package,
                "version" => $this->Version,
                "source" => $this->Source,
                "required" => $this->Required
            );
        }

        /**
         * @param array $data
         * @return Dependency
         * @throws InvalidDependencyException
         */
        public static function fromArray(array $data): Dependency
        {
            $DependencyObject = new Dependency();

            if(isset($data["package"]))
            {
                $DependencyObject->Package = $data["package"];
            }
            else
            {
                throw new InvalidDependencyException("The property 'package' is missing from the dependency");
            }

            if(isset($data["version"]))
            {
                $DependencyObject->Version = $data["version"];
            }
            else
            {
                throw new InvalidDependencyException("The property 'version' is missing from the dependency");
            }

            if(isset($data["source"]))
            {
                $DependencyObject->Source = $data["source"];
            }

            if(isset($data["required"]))
            {
                $DependencyObject->Required = (bool)$data["required"];
            }
            else
            {
                throw new InvalidDependencyException("The property 'required' is missing from the dependency");
            }

            return $DependencyObject;
        }
    }