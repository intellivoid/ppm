<?php


    namespace PpmPython\Abstracts;

    /**
     * Class PackageInstallType
     * @package PpmPython\Abstracts
     */
    abstract class PackageInstallType
    {
        /**
         * Indicates the package is installed on the system and can be accessed from anywhere.
         */
        const System = "SYSTEM";

        /**
         * Indicates the package is installed on the environment and can only be used from that environment
         */
        const Environment = "ENVIRONMENT";
    }