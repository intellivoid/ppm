<?php


    namespace ppm\Objects;


    use Exception;
    use ppm\Exceptions\InvalidPackageLockException;
    use ppm\Exceptions\PackageNotFoundException;
    use ppm\Objects\PackageLock\PackageLockItem;
    use ppm\Objects\PackageLock\VersionConfiguration;

    /**
     * Class PackageLock
     * @package ppm\Objects
     */
    class PackageLock
    {
        /**
         * @var string
         */
        public $PpmVersion;

        /**
         * @var PackageLockItem[]
         */
        public $Packages;

        /**
         * PackageLock constructor.
         */
        public function __construct()
        {
            $this->PpmVersion = PPM_VERSION;
            $this->Packages = array();
        }

        /**
         * @param string $package
         * @return PackageLockItem
         * @throws PackageNotFoundException
         */
        public function getPackage(string $package): PackageLockItem
        {
            if(isset($this->Packages[$package]) == false)
            {
                throw new PackageNotFoundException("The package $package was not found in the package lock");
            }

            return $this->Packages[$package];
        }

        /**
         * @param Package $package
         * @return PackageLockItem
         */
        public function addPackage(Package $package): PackageLockItem
        {
            if(isset($this->Packages[$package->Metadata->PackageName]))
            {
                $PackageLockItem = $this->Packages[$package->Metadata->PackageName];
            }
            else
            {
                $PackageLockItem = new PackageLockItem();
                $PackageLockItem->PackageName = $package->Metadata->PackageName;

            }

            $VersionConfiguration = new VersionConfiguration();
            $VersionConfiguration->Version = $package->Metadata->Version;
            $VersionConfiguration->Dependencies = $package->Dependencies;
            $VersionConfiguration->AutoloadMethod = $package->Configuration->AutoLoadMethod;
            $VersionConfiguration->Main = $package->Configuration->Main;

            $PackageLockItem->addVersion($package->Metadata->Version, $VersionConfiguration);
            $this->Packages[$PackageLockItem->PackageName] = $PackageLockItem;

            return $PackageLockItem;
        }

        /**
         * @param string $package_name
         * @param string $version
         * @return bool
         */
        public function removePackage(string $package_name, string $version="all"): bool
        {
            if(isset($this->Packages[$package_name]) == false)
            {
                return false;
            }

            if($version == "all")
            {
                unset($this->Packages[$package_name]);
                return true;
            }


            /** @var PackageLockItem $package_lock */
            $package_lock = $this->Packages[$package_name];

            if($version == "latest")
            {
                $version = $package_lock->getLatestVersion();
            }

            $this->Packages[$package_name]->removeVersion($version);
            $this->clearUnusable();

            return true;
        }

        /**
         * @param string $package_name
         * @param string $version
         * @return bool
         */
        public function packageExists(string $package_name, string $version="latest"): bool
        {
            if(isset($this->Packages[$package_name]) == false)
            {
                return false;
            }

            /** @var PackageLockItem $package_lock */
            $package_lock = $this->Packages[$package_name];

            if($version == "latest")
            {
                $version = $package_lock->getLatestVersion();
            }

            if($version == "all")
            {
                $version = $package_lock->getLatestVersion();
            }

            try
            {
                if($package_lock->validateVersion($version))
                {
                    return true;
                }
            }
            catch(Exception $e)
            {
                unset($e);
            }

            return false;
        }

        /**
         * @return bool
         */
        public function clearUnusable(): bool
        {
            /** @var PackageLockItem $package_lock_item */
            foreach($this->Packages as $package => $package_lock_item)
            {
                if(count($package_lock_item->Versions) == 0)
                {
                    unset($this->Packages[$package]);
                }
            }

            return true;
        }


        /**
         * @return array
         */
        public function toArray(): array
        {
            $package_locks = array();

            /** @var PackageLockItem $package_contents */
            foreach($this->Packages as $package => $package_contents)
            {
                $package_locks[$package] = $package_contents->toArray();
            }

            return array(
                'ppm_version' => $this->PpmVersion,
                'packages' => $package_locks
            );
        }

        /**
         * @param array $data
         * @return PackageLock
         * @throws InvalidPackageLockException
         */
        public static function fromArray(array $data): PackageLock
        {
            $PackageLockObject = new PackageLock();

            if(isset($data['ppm_version']))
            {
                $PackageLockObject->PpmVersion = $data['ppm_version'];
            }
            else
            {
                throw new InvalidPackageLockException("The property 'ppm_version' was not found in the Package Lock which is required to determine if the package lock is compatible with ppm");
            }

            if(isset($data['packages']))
            {
                foreach($data['packages'] as $package => $package_contents)
                {
                    $PackageLockObject->Packages[$package] = PackageLockItem::fromArray($package_contents, $package);
                }
            }
            else
            {
                throw new InvalidPackageLockException("The property 'packages' was not found in the Package Lock");
            }

            return $PackageLockObject;
        }
    }