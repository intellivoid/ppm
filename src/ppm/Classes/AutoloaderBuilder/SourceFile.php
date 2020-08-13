<?php


    namespace ppm\Classes\AutoloaderBuilder;


    use SplFileInfo;

    /**
     * Class SourceFile
     * @package ppm\Exceptions
     */
    class SourceFile extends SplFileInfo
    {
        /**
         * @return array
         */
        public function getTokens()
        {
            return token_get_all(file_get_contents($this->getRealPath()));
        }
    }