<?php


    namespace ppm\Classes\AutoloaderBuilder;

    /**
     * Class CachingParser
     * @package ppm\Exceptions
     */
    class CachingParser implements ParserInterface
    {

        /**
         * @var ParserInterface
         */
        private $parser;

        /**
         * @var Cache
         */
        private $cache;

        /**
         * CachingParser constructor.
         * @param Cache $cache
         * @param ParserInterface $parser
         */
        public function __construct(Cache $cache, ParserInterface $parser)
        {
            $this->cache = $cache;
            $this->parser = $parser;
        }

        /**
         * Parse a given file for defintions of classes, traits and interfaces
         *
         * @param SourceFile $source file to process
         *
         * @return ParseResult
         */
        public function parse(SourceFile $source)
        {
            if ($this->cache->hasResult($source))
            {
                return $this->cache->getResult($source);
            }

            $result = $this->parser->parse($source);
            $this->cache->addResult($source, $result);
            return $result;
        }

    }