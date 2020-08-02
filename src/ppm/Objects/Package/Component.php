<?php


    namespace ppm\Objects\Package;

    use ppm\Exceptions\InvalidComponentException;
    use PpmParser\Node\Stmt;
    use PpmParser\NodeDumper;
    use PpmParser\ParserFactory;

    /**
     * Class Component
     * @package ppm\Objects\Package
     */
    class Component
    {
        /**
         * @var string
         */
        public $BaseDirectory;

        /**
         * @var bool
         */
        public $Required;

        /**
         * @var string
         */
        public $File;

        /**
         * @param string|null $base_directory
         * @return string
         */
        public function getPath(string $base_directory=null): string
        {
            if(is_null($base_directory))
            {
                $base_directory = $this->BaseDirectory;
            }

            return $base_directory . DIRECTORY_SEPARATOR . str_ireplace('/', DIRECTORY_SEPARATOR, $this->File);
        }

        /**
         * @param string|null $base_directory
         * @return Stmt[]|null
         */
        public function parse(string $base_directory=null)
        {
            $source_code = file_get_contents($this->getPath($base_directory));
            $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
            return $parser->parse($source_code);
        }

        /**
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'required' => $this->Required,
                'file' => $this->File
            );
        }

        /**
         * @param array $data
         * @param string $base_directory
         * @return Component
         * @throws InvalidComponentException
         */
        public static function fromArray(array $data, string $base_directory): Component
        {
            $ComponentObject = new Component();
            $ComponentObject->BaseDirectory = $base_directory;

            if(isset($data['required']))
            {
                $ComponentObject->Required = (bool)$data['required'];
            }
            else
            {
                throw new InvalidComponentException("The component requires the 'required' property");
            }

            if(isset($data['file']))
            {
                $ComponentObject->File = $data['file'];
            }
            else
            {
                throw new InvalidComponentException("The component requires the 'file' property");
            }

            return $ComponentObject;
        }
    }