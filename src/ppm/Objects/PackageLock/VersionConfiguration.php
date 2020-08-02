<?php


    namespace ppm\Objects\PackageLock;


    use Exception;
    use ppm\Abstracts\AutoloadMethod;
    use ppm\Exceptions\InvalidPackageLockException;
    use ppm\Objects\Package\Configuration\MainExecution;
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
         * @var MainExecution|null
         */
        public $Main;

        /**
         * VersionConfiguration constructor.
         */
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
            $main = null;

            if($this->Main !== null)
            {
                $main = $this->Main->toArray();
            }

            /** @var Dependency $dependency */
            foreach($this->Dependencies as $dependency)
            {
                $dependencies[] = $dependency->toArray();
            }

            return array(
                'dependencies' => $dependencies,
                'autoload_method' => $this->AutoloadMethod,
                'main' => $main
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
            $VersionConfigurationObject->Version = $version;

            if(isset($data['main']))
            {
                $VersionConfigurationObject->Main = MainExecution::fromArray($data['main']);
            }
            else
            {
                $VersionConfigurationObject->Main = null;
            }

            if(isset($data['autoload_method']))
            {
                $VersionConfigurationObject->AutoloadMethod = $data['autoload_method'];
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