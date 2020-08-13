<?php


    namespace ppm\Classes\AutoloaderBuilder;

    /**
     * Class StaticRequireListRenderer
     * @package ppm\Classes\AutoloaderBuilder
     */
    class StaticRequireListRenderer implements StaticListRenderer
    {

        /** @var boolean */
        private $useOnce;

        /** @var string */
        private $indent;

        /** @var string */
        private $linebreak;

        /**
         * @param $useOnce
         * @param $indent
         * @param $linebreak
         */
        public function __construct($useOnce, $indent, $linebreak)
        {
            $this->useOnce = $useOnce;
            $this->indent = $indent;
            $this->linebreak = $linebreak;
        }

        /**
         * @param array $list
         * @return string
         */
        public function render(array $list)
        {
            $require = (boolean)$this->useOnce ? 'require_once' : 'require';
            $require .= ' ___BASEDIR___\'';
            $glue = '\';' . $this->linebreak . $this->indent . $require;

            return $this->indent . $require . implode($glue, $list) . '\';';
        }
    }
