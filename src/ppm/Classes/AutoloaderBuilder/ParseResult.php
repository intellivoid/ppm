<?php


    namespace ppm\Classes\AutoloaderBuilder;

    /**
     * Class ParseResult
     * @package ppm\Exceptions
     */
    class ParseResult
    {

        /**
         * @var string[]
         */
        private $units = array();

        /**
         * @var array
         */
        private $dependencies = array();

        /**
         * @var string[]
         */
        private $redeclarations = array();

        /**
         * ParseResult constructor.
         * @param array $units
         * @param array $dependencies
         * @param array $redeclarations
         */
        public function __construct(Array $units, Array $dependencies, Array $redeclarations)
        {
            $this->units = $units;
            $this->dependencies = $dependencies;
            $this->redeclarations = $redeclarations;
        }

        public function hasUnits()
        {
            return count($this->units) > 0;
        }

        public function hasRedeclarations()
        {
            return count($this->redeclarations) > 0;
        }

        /**
         *
         * @param string $unit
         *
         * @return array
         */
        public function getDependenciesForUnit($unit)
        {
            if (!isset($this->dependencies[$unit]))
            {
                return array();
            }
            return $this->dependencies[$unit];
        }

        /**
         * @return string[]
         */
        public function getRedeclarations()
        {
            return $this->redeclarations;
        }

        /**
         * @return string[]
         */
        public function getUnits()
        {
            return $this->units;
        }

    }