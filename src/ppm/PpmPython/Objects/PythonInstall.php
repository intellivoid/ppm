<?php /** @noinspection PhpMissingFieldTypeInspection */


namespace PpmPython\Objects;

    /**
     * Class PythonInstall
     * @package PpmPython\Objects
     */
    class PythonInstall
    {
        /**
         * The version of the python install
         *
         * @var string
         */
        public $Version;

        /**
         * The installation path for this version of Python
         *
         * @var string
         */
        public $Path;

        /**
         * The installation details for pip
         *
         * @var PipInstall
         */
        public $PipInstall;

        /**
         * An array of installed packages for this install
         *
         * @var InstalledPackage[]
         */
        public $InstalledPackages;

        /**
         * Paths where Python loads the builtin modules
         *
         * @var string
         */
        public $SharedLibraryPath;

        /**
         * The paths where Python stores site-packages at
         *
         * @var string[]
         */
        public $SitePackagesPaths;

        /**
         * @return array
         */
        public function toArray(): array
        {

        }
    }