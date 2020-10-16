<?php


    namespace ppm\Classes\Composer\Service;


    use ArrayAccess;
    use Iterator;

    /**
     * This class represents a map from namespaces to sources.
     *
     * Class NamespaceMap
     * @package ppm\Classes\Composer\Service
     */
    class NamespaceMap extends AbstractMap implements Iterator, ArrayAccess
    {
        /**
         * @param array $data The composer.json partial data
         * @see AbstractMap::__construct
         */
        public function __construct(array $data = [])
        {
            $this->mapKeys = [
                'key' => 'namespace',
                'value' => 'source'
            ];

            parent::__construct($data);
        }

        /**
         * Gets the parsed namespace map as an array of objects.
         *
         * @return array
         */
        public function getNamespaces(): array
        {
            return $this->getRawData();
        }

    }