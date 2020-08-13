<?php


    namespace ppm\Classes\AutoloaderBuilder;

    /**
     * Class CacheWarmingListRenderer
     * @package ppm\Classes\AutoloaderBuilder
     */
    class CacheWarmingListRenderer implements StaticListRenderer
    {
        /**
         * @var bool
         */
        private $addReset;

        /**
         * @var string
         */
        private $indent;

        private $linebreak;

        /**
         * @param boolean $addReset
         * @param string $indent
         * @param $linebreak
         */
        public function __construct($addReset, $indent, $linebreak)
        {
            $this->addReset = $addReset;
            $this->indent = $indent;
            $this->linebreak = $linebreak;
        }

        /**
         * @param array $list
         * @return string
         */
        public function render(array $list)
        {
            $line = $this->indent . 'opcache_compile_file(___BASEDIR___\'';
            $glue = '\');' . $this->linebreak . $line;

            $firstLine = $this->addReset ? $this->indent . 'opcache_reset();' . $this->linebreak : '';
            return $firstLine . $line . implode($glue, $list) . '\');';

        }
    }
