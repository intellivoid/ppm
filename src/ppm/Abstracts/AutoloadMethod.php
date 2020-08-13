<?php


    namespace ppm\Abstracts;

    /**
     * Class AutoloadMethod
     * @package ppm\Abstracts
     */
    abstract class AutoloadMethod
    {
        /**
         * Auto loads all components static by order from the Components array in
         * the package configuration file (package.json)
         *
         * This method can be slow for large packages and requires the component
         * order to be correct otherwise the loading may fail
         */
        const Static = "static";

        /**
         * Indexes the components during runtime with a cache-helper system and registers
         * and auto loader for the imported package
         *
         * Fast during runtime but can often cause issues when importing packages dynamically
         * due to an out-dated cache (if a lot of programs uses PPM)
         */
        const Indexed = "indexed";

    }