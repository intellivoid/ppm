<?php


    namespace ppm\Objects\Package;


    use ppm\Abstracts\AutoloadMethod;
    use ppm\Exceptions\InvalidConfigurationException;

    /**
     * Class Configuration
     * @package ppm\Objects\Package
     */
    class Configuration
    {
        /**
         * @var string|AutoloadMethod
         */
        public $AutoLoadMethod;

        /**
         * @var string|null
         */
        public $CliMain;

        /**
         * @var array
         */
        public $PostExecution;

        /**
         * @var array
         */
        public $FinalExecution;

        /**
         * Configuration constructor.
         */
        public function __construct()
        {
            $this->PostExecution = [];
            $this->FinalExecution = [];
        }

        /**
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'autoload_method' => $this->AutoLoadMethod,
                'cli_main' => $this->CliMain,
                'post_execution' => $this->PostExecution,
                'final_execution' => $this->FinalExecution
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
            }
            else
            {
                throw new InvalidConfigurationException("The configuration is missing the property 'autoload_method'");
            }

            if(isset($data['cli_main']))
            {
                $ConfigurationObject->CliMain = $data['cli_main'];
            }
            else
            {
                throw new InvalidConfigurationException("The configuration is missing the property 'cli_main'");
            }

            if(isset($data['post_execution']))
            {
                $ConfigurationObject->PostExecution = $data['post_execution'];
            }
            else
            {
                throw new InvalidConfigurationException("The configuration is missing the property 'post_execution'");
            }

            if(isset($data['final_execution']))
            {
                $ConfigurationObject->FinalExecution = $data['final_execution'];
            }
            else
            {
                throw new InvalidConfigurationException("The configuration is missing the property 'final_execution'");
            }

            return $ConfigurationObject;
        }
    }