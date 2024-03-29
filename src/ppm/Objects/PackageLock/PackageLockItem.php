<?php


    namespace ppm\Objects\PackageLock;


    use ppm\Abstracts\AutoloadMethod;
    use ppm\Exceptions\AutoloaderException;
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
         * @var VersionConfiguration[]
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

                case AutoloadMethod::ComposerGenerator:
                    $composer_autoloader_path = $this->getPackagePath($version) . DIRECTORY_SEPARATOR . "autoload.php";

                    if(file_exists($composer_autoloader_path) == false)
                    {
                        throw new AutoloaderException("The autoloading method 'ComposerGenerator' failed because the file '" . $composer_autoloader_path . "' does not exist.");
                    }

                    /** @noinspection PhpIncludeInspection */
                    require_once($composer_autoloader_path);
                    return true;

                case AutoloadMethod::GeneratedStatic:
                case AutoloadMethod::StandardPhpLibrary:
                    $DataPath = $this->getPackagePath($version) . DIRECTORY_SEPARATOR . '.ppm';

                    if(file_exists($DataPath . DIRECTORY_SEPARATOR . 'AUTOLOADER') == false)
                    {
                        throw new AutoloaderException("The autoloading method 'GeneratedStatic' failed because the file '" . $DataPath . DIRECTORY_SEPARATOR . 'AUTOLOADER' . "' does not exist.");
                    }

                    /** @noinspection PhpIncludeInspection */
                    require_once(sprintf("%s%sAUTOLOADER", $DataPath, DIRECTORY_SEPARATOR));

                    if(file_exists($DataPath . DIRECTORY_SEPARATOR . 'AUTOLOADER_UNITS'))
                    {
                        /** @noinspection PhpIncludeInspection */
                        require_once(sprintf("%s%sAUTOLOADER_UNITS", $DataPath, DIRECTORY_SEPARATOR));
                    }

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

            if(isset($this->Versions[0]) == false)
            {
                $FixedResults = array();

                foreach($this->Versions as $key=>$value)
                {
                    $FixedResults[] = $value;
                }

                $this->Versions = $FixedResults;
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

                if(isset($PackageLockItem->Versions[0]) == false)
                {
                    $FixedResults = array();

                    foreach($PackageLockItem->Versions as $key=>$value)
                    {
                        $FixedResults[] = $value;
                    }

                    $PackageLockItem->Versions = $FixedResults;
                }
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