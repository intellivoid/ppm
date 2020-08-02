<?php


    namespace ppm\Objects\Package\Configuration;


    /**
     * Class MainExecution
     * @package ppm\Objects\Package\Configuration
     */
    class MainExecution
    {
        /**
         * The location of the main execution point
         *
         * @var string
         */
        public $ExecutionPoint;

        /**
         * Create a Symbolic link for the system to execute?
         *
         * @var bool
         */
        public $CreateSymlink;

        /**
         * The name of the symbolic link to create that points to the execution point
         *
         * @var string
         */
        public $Name;

        /**
         * Returns an array which represents this object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                "execution_point" => $this->ExecutionPoint,
                "create_symlink" => (bool)$this->CreateSymlink,
                "name" => $this->Name
            );
        }

        /**
         * Constructs object from array
         *
         * @param array $data
         * @return MainExecution
         */
        public static function fromArray(array $data): MainExecution
        {
            $MainExecutionObject = new MainExecution();

            if(isset($data["execution_point"]))
            {
                $MainExecutionObject->ExecutionPoint = $data["execution_point"];
            }

            if(isset($data["create_symlink"]))
            {
                $MainExecutionObject->CreateSymlink = (bool)$data["create_symlink"];
            }

            if(isset($data["name"]))
            {
                $MainExecutionObject->Name = $data["name"];
            }

            return $MainExecutionObject;
        }
    }