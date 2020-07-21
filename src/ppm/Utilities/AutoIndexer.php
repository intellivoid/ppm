<?php

    declare(strict_types=1);

    namespace ppm\Utilities;

    /**
     * Class AutoIndexer
     * @package ppm\Utilities
     */
    class AutoIndexer
    {
        private const RETRY_LIMIT = 3;

        /** @var string[] */
        public $ignoreDirs = ['.*', '*.old', '*.bak', '*.tmp', 'temp'];

        /** @var string[] */
        public $acceptFiles = ['*.php'];

        /** @var bool */
        private $autoRebuild = true;

        /** @var bool */
        private $reportParseErrors = true;

        /** @var string[] */
        private $scanPaths = [];

        /** @var string[] */
        private $excludeDirs = [];

        /** @var array of class => [file, time] */
        private $classes = [];

        /** @var bool */
        private $cacheLoaded = false;

        /** @var bool */
        private $refreshed = false;

        /** @var array of missing classes */
        private $missing = [];

        /** @var string|null */
        private $tempDirectory;
    }