<?php


    namespace ppm\Classes\AutoloaderBuilder;


    /**
     * Namespace aware parser to find and extract defined classes within php source files
     */
    interface ParserInterface
    {

        /**
         * Parse a given file for definitions of classes, traits and interfaces
         *
         * @param SourceFile $source file to process
         *
         * @return ParseResult
         */
        public function parse(SourceFile $source);
    }
