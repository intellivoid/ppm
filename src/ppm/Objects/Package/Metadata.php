<?php


    namespace ppm\Objects\Package;

    use ppm\Exceptions\MissingPackagePropertyException;

    /**
     * Class Metadata
     * @package ppm\Objects\Package
     */
    class Metadata
    {
        /**
         * @var string
         */
        public $PackageName;

        /**
         * @var string
         */
        public $Name;

        /**
         * @var string
         */
        public $Version;

        /**
         * @var string|null
         */
        public $Author;

        /**
         * @var string|null
         */
        public $Organization;

        /**
         * @var string|null
         */
        public $Description;

        /**
         * @var string|null
         */
        public $URL;

        /**
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'package_name' => $this->PackageName,
                'name' => $this->Name,
                'version' => $this->Version,
                'author' => $this->Author,
                'organization' => $this->Organization,
                'description' => $this->Description,
                'url' => $this->URL
            );
        }

        /**
         * @param array $data
         * @return Metadata
         * @throws MissingPackagePropertyException
         */
        public static function fromArray(array $data): Metadata
        {
            $MetadataObject = new Metadata();

            if(isset($data['package_name']))
            {
                $MetadataObject->PackageName = $data['package_name'];
            }
            else
            {
                throw new MissingPackagePropertyException("The property 'package_name' is missing from the package");
            }

            if(isset($data['name']))
            {
                $MetadataObject->Name = $data['name'];
            }
            else
            {
                throw new MissingPackagePropertyException("The property 'name' is missing from the package");
            }

            if(isset($data['version']))
            {
                $MetadataObject->Version = $data['version'];
            }
            else
            {
                throw new MissingPackagePropertyException("The property 'version' is missing from the package");
            }

            if(isset($data['author']))
            {
                $MetadataObject->Author = $data['author'];
            }

            if(isset($data['organization']))
            {
                $MetadataObject->Organization = $data['organization'];
            }

            if(isset($data['description']))
            {
                $MetadataObject->Description = $data['description'];
            }

            if(isset($data['url']))
            {
                $MetadataObject->URL = $data['url'];
            }

            return $MetadataObject;
        }
    }