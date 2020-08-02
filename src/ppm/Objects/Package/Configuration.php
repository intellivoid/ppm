<?php


    namespace ppm\Objects\Package;

    use ppm\Abstracts\AutoloadMethod;
    use ppm\Exceptions\InvalidConfigurationException;
    use ppm\Objects\Package\Configuration\MainExecution;

    /**
     * Class Configuration
     * @package ppm\Objects\Package
     */
    class Configuration
    {
        /**
         * The autoload method used to import this package
         *
         * @var string|AutoloadMethod
         */
        public $AutoLoadMethod;

        /**
         * The main execution point if this component has any
         *
         * @var MainExecution|null
         */
        public $Main;

        /**
         * The scripts to execute post
         *
         * @var array
         */
        public $PostInstallation;

        /**
         * @var array
         */
        public $PreInstallation;

        /**
         * Configuration constructor.
         */
        public function __construct()
        {
            $this->PostInstallation = [];
            $this->PreInstallation = [];
        }

        /**
         * @return array
         */
        public function toArray(): array
        {
            $main = null;

            if($this->Main !== null)
            {
                $main = $this->Main->toArray();
            }

            return array(
                'autoload_method' => $this->AutoLoadMethod,
                'main' => $main,
                'post_installation' => $this->PostInstallation,
                'pre_installation' => $this->PreInstallation
            );
        }

        /**
         * @param array $data
         * @return Configuration
         * @throws InvalidConfigurationException
         */
        public static function fromArray(array $data): Configuration
        {
            $ConfigurationObject = new Configuration();

            if(isset($data['autoload_method']))
            {
                $ConfigurationObject->AutoLoadMethod = $data['autoload_method'];
                // TODO: Validate autoload method
            }
            else
            {
                throw new InvalidConfigurationException("The configuration is missing the property 'autoload_method'");
            }

            if(isset($data['main']))
            {
                $ConfigurationObject->Main = MainExecution::fromArray($data["main"]);
            }
            else
            {
                $ConfigurationObject->Main = null;
            }

            if(isset($data['post_installation']))
            {
                $ConfigurationObject->PostInstallation = $data['post_installation'];
            }
            else
            {
                throw new InvalidConfigurationException("The configuration is missing the property 'post_installation'");
            }

            if(isset($data['pre_installation']))
            {
                $ConfigurationObject->PreInstallation = $data['pre_installation'];
            }
            else
            {
                throw new InvalidConfigurationException("The configuration is missing the property 'pre_installation'");
            }

            return $ConfigurationObject;
        }
    }