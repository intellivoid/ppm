<?php


    namespace ppm\Objects;

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

        public function getPackageConfigurationPath(): string
        {
            return $this->Path . DIRECTORY_SEPARATOR . 'package.json';
        }
    }