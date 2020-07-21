<?php

    declare(strict_types=1);

    namespace ppm\Utilities;

    use ppm\Exceptions\NotSupportedException;

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

        /**
         * AutoIndexer constructor.
         */
        public function __construct()
        {
            if (!extension_loaded('tokenizer'))
            {
                throw new NotSupportedException('PHP extension Tokenizer is not loaded.');
            }
        }

        /**
         * Register autoloader.
         *
         * @param bool $prepend
         * @return $this
         */
        public function register(bool $prepend = false): self
        {
            spl_autoload_register([$this, 'tryLoad'], true, $prepend);
            return $this;
        }

        /**
         * Handles autoloading of classes, interfaces or traits.
         *
         * @param string $type
         */
        public function tryLoad(string $type): void
        {
            $this->loadCache();
            $type = ltrim($type, '\\'); // PHP namespace bug #49143
            $info = $this->classes[$type] ?? null;

            if ($this->autoRebuild) {
                if (!$info || !is_file($info['file']))
                {
                    $missing = &$this->missing[$type];
                    $missing++;
                    if (!$this->refreshed && $missing <= self::RETRY_LIMIT)
                    {
                        $this->refreshClasses();
                        $this->saveCache();
                    }
                    elseif ($info)
                    {
                        unset($this->classes[$type]);
                        $this->saveCache();
                    }

                }
                elseif (!$this->refreshed && filemtime($info['file']) !== $info['time'])
                {
                    $this->updateFile($info['file']);
                    if (empty($this->classes[$type]))
                    {
                        $this->missing[$type] = 0;
                    }
                    $this->saveCache();
                }
                $info = $this->classes[$type] ?? null;
            }

            if ($info)
            {
                (static function ($file) {
                    /** @noinspection PhpIncludeInspection */
                    require $file; })($info['file']);
            }
        }

        /**
         * Add path or paths to list.
         *
         * @param mixed ...$paths
         * @return $this
         */
        public function addDirectory(...$paths): self
        {
            if (is_array($paths[0] ?? null)) {
                trigger_error(__METHOD__ . '() use var
                Add path or paths to list.
                iadics ...$paths to add an array of paths.', E_USER_WARNING);
                $paths = $paths[0];
            }
            $this->scanPaths = array_merge($this->scanPaths, $paths);
            return $this;
        }

        /**
         * @param bool $on
         * @return $this
         */
        public function reportParseErrors(bool $on = true): self
        {
            $this->reportParseErrors = $on;
            return $this;
        }

        /**
         * Excludes path or paths from list.
         *
         * @param mixed ...$paths
         * @return $this
         */
        public function excludeDirectory(...$paths): self
        {
            if (is_array($paths[0] ?? null)) {
                trigger_error(__METHOD__ . '() use variadics ...$paths to add an array of paths.', E_USER_WARNING);
                $paths = $paths[0];
            }
            $this->excludeDirs = array_merge($this->excludeDirs, $paths);
            return $this;
        }

        /**
         * @return array
         */
        public function getIndexedClasses(): array
        {
            $this->loadCache();
            $res = [];
            foreach ($this->classes as $class => $info) {
                $res[$class] = $info['file'];
            }
            return $res;
        }
    }