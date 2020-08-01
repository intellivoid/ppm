<?php


    namespace ppm\Objects\PackageLock;


    use ppm\Abstracts\AutoloadMethod;
    use ppm\Exceptions\AutoloaderException;
    use ppm\Exceptions\InvalidComponentException;
    use ppm\Exceptions\InvalidPackageLockException;
    use ppm\Exceptions\VersionNotFoundException;
    use ppm\Utilities\Autoloader;
    use ppm\Utilities\PathFinder;

    /**
     * Class PackageLockItem
     * @package ppm\Objects\PackageLock
     */
    class PackageLockItem
    {
        /**
         * @var
         */
        public $PackageName;

        /**
         * @var array
         */
        public $Versions;

        /**
         * @var array
         */
        public $VersionConfigurations;

        /**
         * PackageLockItem constructor.
         */
        public function __construct()
        {
            $this->VersionConfigurations = array();
            $this->Versions = [];
        }

        /**
         * @param string $version
         * @param VersionConfiguration $versionConfiguration
         * @return bool
         */
        public function addVersion(string $version, VersionConfiguration $versionConfiguration): bool
        {
            if(in_array($version, $this->Versions))
            {
                return false;
            }

            $this->Versions[] = $version;
            $this->VersionConfigurations[$version] = $versionConfiguration;

            return true;
        }

        /**
         * @param string $version
         * @return bool
         */
        public function removeVersion(string $version): bool
        {
            if(in_array($version, $this->Versions) == false)
            {
                return false;
            }
            
            $this->Versions = array_diff($this->Versions, array($version));
            unset($this->VersionConfigurations[$version]);

            return true;
        }

        /**
         * @param string $version
         * @return bool
         * @throws VersionNotFoundException
         */
        public function validateVersion(string $version="latest"): bool
        {
            if($version == "latest")
            {
                $version = $this->getLatestVersion();
            }

            if(in_array($version, $this->Versions) == false)
            {
                throw new VersionNotFoundException("The version '$version' of the package " . $this->PackageName . " is not installed (Version not found)");
            }

            if(isset($this->VersionConfigurations[$version]) == false)
            {
                throw new VersionNotFoundException("The version '$version' of the package " . $this->PackageName . " is not installed (Version configuration in package lock not found)");
            }

            return true;
        }

        /**
         * @param string $version
         * @return string
         * @throws VersionNotFoundException
         */
        public function getPackagePath(string $version="latest"): string
        {
            self::validateVersion($version);
            return PathFinder::getPackagePath($this->PackageName, $version);
        }

        /**
         * @param string $version
         * @return bool
         * @throws AutoloaderException
         * @throws VersionNotFoundException
         * @throws InvalidComponentException
         */
        public function import(string $version="latest"): bool
        {
            if($version == "latest")
            {
                $version = self::getLatestVersion();
            }

            self::validateVersion($version);

            /** @var VersionConfiguration $Configuration */
            $Configuration = $this->VersionConfigurations[$version];

            switch($Configuration->AutoloadMethod)
            {
                case AutoloadMethod::Static:
                    Autoloader::loadStaticLoader($this->getPackagePath($version));
                    return true;

                case AutoloadMethod::Indexed:
                    Autoloader::loadIndexedLoader($this->getPackagePath($version));
                    return true;

                default:
                    throw new AutoloaderException("The autoloader method '" . $Configuration->AutoloadMethod . "' is not supported.");
            }
        }

        /**
         * @return string
         */
        public function getLatestVersion(): string
        {
            $latest_version = $this->Versions[0];

            foreach($this->Versions as $version)
            {
                if(version_compare($version, $latest_version, ">"))
                {
                    $latest_version = $version;
                }
            }

            return $latest_version;
        }


        /**
         * @return array
         */
        public function toArray(): array
        {
            $version_configurations = array();

            /** @var VersionConfiguration $versionConfiguration */
            foreach($this->VersionConfigurations as $versionName => $versionConfiguration)
            {
                $version_configurations[$versionName] = $versionConfiguration->toArray();
            }

            return array(
                'versions' => $this->Versions,
                'version_configurations' => $version_configurations
            );
        }

        /**
         * @param array $data
         * @param string $package_name
         * @return PackageLockItem
         * @throws InvalidPackageLockException
         */
        public static function fromArray(array $data, string $package_name): PackageLockItem
        {
            $PackageLockItem = new PackageLockItem();
            $PackageLockItem->PackageName = $package_name;

            if(isset($data['versions']))
            {
                $PackageLockItem->Versions = $data['versions'];
            }
            else
            {
                throw new InvalidPackageLockException("The package lock '$package_name' is missing the property 'versions'");
            }

            if(isset($data['version_configurations']))
            {
                foreach($data['version_configurations'] as $version_name => $version_configuration)
                {
                    $PackageLockItem->VersionConfigurations[$version_name] = VersionConfiguration::fromArray(
                        $version_configuration, $version_name
                    );
                }
            }
            else
            {
                throw new InvalidPackageLockException("The package lock '$package_name' is missing the property 'version_configurations'");
            }

            return $PackageLockItem;
        }
    }