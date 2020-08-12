<?php

    namespace ppm\Classes\DirectoryScanner;

    /**
     * Class Exception
     * @package ppm\Classes\DirectoryScanner
     */
    class Exception extends \Exception
    {

        /**
         * Error constant for "notFound" condition
         *
         * @var integer
         */
        const NotFound = 1;

        /**
         *  Error condition for invalid flag passed to setFlag/unsetFlag method
         *
         * @var integer
         */
        const InvalidFlag = 2;
    }