<?php

    declare(strict_types=1);

    namespace ppm\Utilities;

    use ppm\Exceptions\InvalidStateException;
    use ppm\Exceptions\IOException;
    use ppm\Exceptions\NotSupportedException;
    use SplFileInfo;

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

        /**
         * Rebuilds class list cache.
         */
        public function rebuild(): void
        {
            $this->cacheLoaded = true;
            $this->classes = $this->missing = [];
            $this->refreshClasses();
            if ($this->tempDirectory) {
                $this->saveCache();
            }
        }

        /**
         * Refreshes class list cache.
         */
        public function refresh(): void
        {
            $this->loadCache();
            if (!$this->refreshed) {
                $this->refreshClasses();
                $this->saveCache();
            }
        }

        /**
         * Refreshes $classes.
         */
        private function refreshClasses(): void
        {
            $this->refreshed = true; // prevents calling refreshClasses() or updateFile() in tryLoad()
            $files = [];
            foreach ($this->classes as $class => $info) {
                $files[$info['file']]['time'] = $info['time'];
                $files[$info['file']]['classes'][] = $class;
            }

            $this->classes = [];
            foreach ($this->scanPaths as $path) {
                $iterator = is_file($path) ? [new SplFileInfo($path)] : $this->createFileIterator($path);
                foreach ($iterator as $file) {
                    $file = $file->getPathname();
                    if (isset($files[$file]) && $files[$file]['time'] == filemtime($file)) {
                        $classes = $files[$file]['classes'];
                    } else {
                        $classes = $this->scanPhp($file);
                    }
                    $files[$file] = ['classes' => [], 'time' => filemtime($file)];

                    foreach ($classes as $class) {
                        $info = &$this->classes[$class];
                        if (isset($info['file'])) {
                            throw new Nette\InvalidStateException("Ambiguous class $class resolution; defined in {$info['file']} and in $file.");
                        }
                        $info = ['file' => $file, 'time' => filemtime($file)];
                        unset($this->missing[$class]);
                    }
                }
            }
        }

        /**
         * Creates an iterator scanning directory for PHP files, subdirectories and 'netterobots.txt' files.
         *
         * @param string $dir
         * @return Finder
         */
        private function createFileIterator(string $dir): Finder
        {
            if (!is_dir($dir)) {
                throw new IOException("File or directory '$dir' not found.");
            }

            if (is_string($ignoreDirs = $this->ignoreDirs))
            {
                trigger_error(__CLASS__ . ': $ignoreDirs must be an array.', E_USER_WARNING);
                $ignoreDirs = preg_split('#[,\s]+#', (string)$ignoreDirs);
            }
            $disallow = [];
            foreach (array_merge($ignoreDirs, $this->excludeDirs) as $item) {
                if ($item = realpath($item)) {
                    $disallow[str_replace('\\', '/', $item)] = true;
                }
            }

            if (is_string($acceptFiles = $this->acceptFiles)) {
                trigger_error(__CLASS__ . ': $acceptFiles must be an array.', E_USER_WARNING);
                $acceptFiles = preg_split('#[,\s]+#', (string)$acceptFiles);
            }

            $iterator = Finder::findFiles($acceptFiles)
                ->filter(function (SplFileInfo $file) use (&$disallow) {
                    return !isset($disallow[str_replace('\\', '/', $file->getRealPath())]);
                })
                ->from($dir)
                ->exclude($ignoreDirs)
                ->filter($filter = function (SplFileInfo $dir) use (&$disallow) {
                    $path = str_replace('\\', '/', $dir->getRealPath());
                    if (is_file("$path/netterobots.txt")) {
                        foreach (file("$path/netterobots.txt") as $s) {
                            if (preg_match('#^(?:disallow\\s*:)?\\s*(\\S+)#i', $s, $matches)) {
                                $disallow[$path . rtrim('/' . ltrim($matches[1], '/'), '/')] = true;
                            }
                        }
                    }
                    return !isset($disallow[$path]);
                });

            $filter(new SplFileInfo($dir));
            return $iterator;
        }

        /**
         * @param string $file
         */
        private function updateFile(string $file): void
        {
            foreach ($this->classes as $class => $info) {
                if (isset($info['file']) && $info['file'] === $file) {
                    unset($this->classes[$class]);
                }
            }

            $classes = is_file($file) ? $this->scanPhp($file) : [];
            foreach ($classes as $class) {
                $info = &$this->classes[$class];
                if (isset($info['file']) && @filemtime($info['file']) !== $info['time']) { // @ file may not exists
                    $this->updateFile($info['file']);
                    $info = &$this->classes[$class];
                }
                if (isset($info['file'])) {
                    throw new InvalidStateException("Ambiguous class $class resolution; defined in {$info['file']} and in $file.");
                }
                $info = ['file' => $file, 'time' => filemtime($file)];
            }
        }
    }