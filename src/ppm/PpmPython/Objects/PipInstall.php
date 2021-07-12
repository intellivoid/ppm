<?php


    namespace PpmPython\Objects;

    /**
     * Class PipInstall
     * @package PpmPython\Objects
     */
    class PipInstall
    {
        /**
         * The version of the pip install
         *
         * @var string
         */
        public $Version;

        /**
         * The installation path for pip
         *
         * @var string
         */
        public $InstallPath;

        /**
         * Returns an array representation of the object
         *
         * @return array
         */
        public function toArray(): array
        {
            return [
                "version" => $this->Version,
                "path" => $this->InstallPath
            ];
        }

        /**
         * Constructs object from array
         *
         * @param array $data
         * @return PipInstall
         */
        public static function fromArray(array $data): PipInstall
        {
            $pipInstallObject = new PipInstall();

            if(isset($data["version"]))
                $pipInstallObject->Version = $data["version"];

            if(isset($data["path"]))
                $pipInstallObject->InstallPath = $data["path"];

            return $pipInstallObject;
        }
    }