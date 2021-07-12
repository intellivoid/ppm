<?php


    namespace PpmPython\Objects;

    use PpmPython\Abstracts\PackageInstallType;

    /**
     * Class InstalledPackage
     * @package PpmPython\Objects
     */
    class InstalledPackage
    {
        /**
         * The name of the package
         *
         * @var string
         */
        public $Name;

        /**
         * The version number of the package
         *
         * @var string
         */
        public $Version;

        /**
         * The install location of the package
         *
         * @var string
         */
        public $InstallPath;

        /**
         * The name of the installer responsible for the installation process
         *
         * @var string
         */
        public $Installer;

        /**
         * Indicates the type of installation of this package, system or environment based.
         *
         * @var string|PackageInstallType
         */
        public $InstallType;

        /**
         * Returns an array representation of the object
         *
         * @return array
         */
        public function toArray(): array
        {
            return [
                "name" => $this->Name,
                "version" => $this->Version,
                "location" => $this->InstallPath,
                "installer" => $this->Installer,
                "install_type" => $this->InstallType
            ];
        }

        public static function fromArray(array $data): InstalledPackage
        {
            $installedPackageObject = new InstalledPackage();

            if(isset($data["name"]))
                $installedPackageObject->Name = $data["name"];

            if(isset($data["version"]))
                $installedPackageObject->Version = $data["version"];

            if(isset($data["location"]))
                $installedPackageObject->InstallPath = $data["location"];

            if(isset($data["installer"]))
                $installedPackageObject->Installer = $data["installer"];

            if(isset($data["install_type"]))
                $installedPackageObject->InstallType = $data["install_type"];

            return $installedPackageObject;
        }
    }