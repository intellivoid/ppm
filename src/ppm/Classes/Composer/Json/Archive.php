<?php


    namespace ppm\Classes\Composer\Json;

    use ppm\Classes\Composer\Service\AbstractClass;

    /**
     * This class represents the "autoload" section in the composer.json schema.
     *
     * Class Archive
     * @package ppm\Classes\Composer\Json
     */
    class Archive extends AbstractClass
    {
        /**
         * @var array
         */
        protected $exclude;

        /**
         * Parses the given data and constructs a new instance from it.
         *
         * @param array $data The composer.json partial data
         */
        public function __construct(array $data = [])
        {
            $this->exclude = (array_key_exists('exclude', $data) ? $data['exclude'] : []);
        }

        /**
         * Get the value of exclude
         *
         * @return  array
         */
        public function getExclude()
        {
            return $this->exclude;
        }
    }