<?php


    namespace ppm\Classes\AutoloaderBuilder;


    use ppm\Classes\DirectoryScanner\PHPFilterIterator;
    use ppm\Exceptions\CollectorException;
    use ppm\Exceptions\CollectorResultException;
    use ppm\Exceptions\ParserException;

    /**
     * Class Collector
     * @package ppm\Classes\AutoloaderBuilder
     */
    class Collector
    {

        /**
         * @var ParserInterface
         */
        private $parser;

        /**
         * @var CollectorResult
         */
        private $collectorResult;

        /**
         * @var bool
         */
        private $tolerantMode;

        /**
         * @var bool
         */
        private $trustingMode;

        /**
         * Collector constructor.
         *
         * @param ParserInterface $parser
         * @param bool            $tolerantMode
         * @param bool            $trustingMode
         * @param array           $whitelist
         * @param array           $blacklist
         */
        public function __construct(ParserInterface $parser, $tolerantMode = false, $trustingMode = true, Array $whitelist = array('*'), Array $blacklist = array())
        {
            $this->parser = $parser;
            $this->tolerantMode = $tolerantMode;
            $this->trustingMode = $trustingMode;
            $this->collectorResult = new CollectorResult($whitelist, $blacklist);
        }

        public function getResult()
        {
            return $this->collectorResult;
        }

        public function processDirectory(\Iterator $sources)
        {
            $worker = $this->trustingMode ? $sources : new PHPFilterIterator($sources);
            foreach($worker as $file)
            {
                $this->processFile($file);
            }
        }

        public function processFile(\SplFileInfo $file)
        {
            if ($this->collectorResult->hasResultFor($file))
            {
                return;
            }
            try
            {
                $parseResult = $this->parser->parse(new SourceFile($file->getRealPath()));
                if ($parseResult->hasRedeclarations() && !$this->tolerantMode)
                {
                    throw new CollectorException(
                        sprintf(
                            "Duplicate (potentially conditional) definitions of the following unit(s) found:\n\n\tUnit(s): %s\n\tFile: %s",
                            join(', ', $parseResult->getRedeclarations()),
                            $file->getRealPath()
                        ),
                        CollectorException::InFileRedeclarationFound
                    );
                }
                $this->collectorResult->addParseResult($file, $parseResult);
            }
            catch(ParserException $e)
            {
                throw new CollectorException(
                    sprintf("Could not process file '%s' due to parse errors: %s", $file->getRealPath(), $e->getMessage()),
                    CollectorException::ParseError, $e
                );
            }
            catch(CollectorResultException $e)
            {
                throw new CollectorException($e->getMessage(), CollectorException::RedeclarationFound);
            }
        }
    }