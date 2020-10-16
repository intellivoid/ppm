<?php


    namespace ppm\Classes\Composer\Service;

    use ArrayAccess;
    use Iterator;

    /**
     * This class represents a map from packages to versions.
     *
     * Class PackageMap
     * @package ppm\Classes\Composer\Service
     */
    class PackageMap extends AbstractMap implements Iterator, ArrayAccess
    {
        /**
         * @see AbstractMap::__construct
         * @param array $data The composer.json partial data
         */
        public function __construct(array $data = [])
        {
            $this->mapKeys = [
                'key' => 'package',
                'value' => 'version'
            ];

            parent::__construct($data);
        }

        /**
         * Gets the parsed package map as an array of objects.
         *
         * @return array
         */
        public function getPackages() : array
        {
            return $this->getRawData();
        }
    }