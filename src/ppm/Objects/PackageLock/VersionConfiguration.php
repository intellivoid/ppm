<?php


    namespace ppm\Objects\PackageLock;


    use Exception;
    use ppm\Abstracts\AutoloadMethod;
    use ppm\Exceptions\InvalidPackageLockException;
    use ppm\Objects\Package\Dependency;

    /**
     * Class VersionConfiguration
     * @package ppm\Objects\PackageLock
     */
    class VersionConfiguration
    {
        /**
         * @var string
         */
        public $Version;

        /**
         * @var array|Dependency
         */
        public $Dependencies;

        /**
         * @var string|AutoloadMethod
         */
        public $AutoloadMethod;

        /**
         * @var string|null
         */
        public $CliMain;

        public function __construct()
        {
            $this->Dependencies = [];
        }

        /**
         * @return array
         */
        public function toArray(): array
        {
            $dependencies = array();

            /** @var Dependency $dependency */
            foreach($this->Dependencies as $dependency)
            {
                $dependencies[] = $dependency->toArray();
            }

            return array(
                'dependencies' => $dependencies,
                'autoload_method' => $this->AutoloadMethod,
                'cli_main' => $this->CliMain
            );
        }

        /**
         * @param array $data
         * @param string $version
         * @return VersionConfiguration
         * @throws InvalidPackageLockException
         */
        public static function fromArray(array $data, string $version): VersionConfiguration
        {
            $VersionConfigurationObject = new VersionConfiguration();

            if(isset($data['cli_main']))
            {
                $VersionConfigurationObject->CliMain = $data['cli_main'];
            }
            else
            {
                throw new InvalidPackageLockException("A version configuration for a package lock is missing 'cli_main'");
            }

            if(isset($data['autoload_method']))
            {
                $VersionConfigurationObject->CliMain = $data['autoload_method'];
            }
            else
            {
                throw new InvalidPackageLockException("A version configuration for a package lock is missing 'autoload_method'");
            }

            if(isset($data['dependencies']))
            {
                foreach($data['dependencies'] as $dependency)
                {
                    try
                    {
                        $VersionConfigurationObject->Dependencies[] = Dependency::fromArray($dependency);
                    }
                    catch(Exception $e)
                    {
                        throw new InvalidPackageLockException(
                            "There was a dependency error for a package lock; " . $e->getMessage());
                    }
                }
            }
            else
            {
                throw new InvalidPackageLockException(
                    "A version configuration for a package lock is missing 'dependencies'");
            }

            return $VersionConfigurationObject;
        }
    }