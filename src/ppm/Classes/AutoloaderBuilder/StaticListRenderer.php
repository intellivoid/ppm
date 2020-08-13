<?php


    namespace ppm\Classes\AutoloaderBuilder;


    /**
     * Interface StaticListRenderer
     * @package ppm\Classes\AutoloaderBuilder
     */
    interface StaticListRenderer
    {

        /**
         * @return string
         */
        public function render(array $list);
    }
