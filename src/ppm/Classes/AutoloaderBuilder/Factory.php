<?php


    namespace ppm\Classes\AutoloaderBuilder;


    use FilesystemIterator;
    use Iterator;
    use ppm\Classes\DirectoryScanner\DirectoryScanner;
    use ppm\Classes\DirectoryScanner\Exception;
    use ppm\Classes\DirectoryScanner\IncludeExcludeFilterIterator;
    use ppm\Exceptions\Config;
    use RuntimeException;

    /**
     * Class Factory
     * @package ppm\Exceptions
     */
    class Factory
    {

        /**
         * @var Config
         */
        private $config;

        /**
         * @var Cache
         */
        private $cache;

        /**
         * @param Config $config
         * @noinspection PhpUnused
         */
        public function setConfig(Config $config)
        {
            $this->config = $config;
        }

        /**
         * @return Application
         * @noinspection PhpUnused
         */
        public function getApplication()
        {
            return new Application($this->config, $this);
        }

        /**
         * @return CachingParser|Parser
         */
        public function getParser()
        {
            $parser = new Parser($this->config->isLowercaseMode());

            if (!$this->config->isCacheEnabled())
            {
                return $parser;
            }

            return new CachingParser($this->getCache(), $parser);
        }

        /**
         * @return Cache
         */
        public function getCache()
        {
            if (!$this->cache instanceof Cache)
            {
                $fname = $this->config->getCacheFile();
                if (file_exists($fname))
                {
                    $data = unserialize(file_get_contents($fname));
                }
                else
                {
                    $data = array();
                }
                $this->cache = new Cache($data);
            }
            return $this->cache;
        }

        public function getCollector()
        {
            return new Collector(
                $this->getParser(),
                $this->config->isTolerantMode(),
                $this->config->isTrustingMode(),
                $this->config->getWhitelist(),
                $this->config->getBlacklist()
            );
        }

        /**
         * Get instance of DirectoryScanner with filter options applied
         *
         * @param bool $filter
         * @return DirectoryScanner
         * @throws Exception
         * @throws Exception
         */
        public function getScanner($filter = TRUE)
        {
            $scanner = new DirectoryScanner;

            if ($filter)
            {
                $scanner->setIncludes($this->config->getInclude());
                $scanner->setExcludes($this->config->getExclude());
            }

            if ($this->config->isFollowSymlinks())
            {
                $scanner->setFlag(FilesystemIterator::FOLLOW_SYMLINKS);
            }

            return $scanner;
        }

        /**
         * @param Iterator $files
         * @return IncludeExcludeFilterIterator
         */
        public function getFilter(Iterator $files)
        {
            $filter = new IncludeExcludeFilterIterator($files);
            $filter->setInclude($this->config->getInclude());
            $filter->setExclude($this->config->getExclude());
            return $filter;
        }

        /**
         * @return PharBuilder
         * @throws Exception
         */
        public function getPharBuilder()
        {
            $builder = new PharBuilder($this->getScanner(!$this->config->isPharAllMode()), $this->config->getBaseDirectory());
            $builder->setCompressionMode($this->config->getPharCompression());
            foreach($this->config->getDirectories() as $directory)
            {
                $builder->addDirectory($directory);
            }

            return $builder;
        }

        /**
         * Helper to get instance of AutoloadRenderer with cli options applied
         *
         * @param CollectorResult $result
         *
         * @return AutoloadRenderer|StaticRenderer
         *@throws RuntimeException
         */
        public function getRenderer(CollectorResult $result)
        {
            $isStatic = $this->config->isStaticMode();
            $isPhar   = $this->config->isPharMode();
            $isCompat = $this->config->isCompatMode();
            $isOnce   = $this->config->isOnceMode();
            $isWarm   = $this->config->isWarmMode();
            $isReset  = $this->config->isResetMode();

            if ($isWarm === TRUE)
            {
                $renderer = new StaticRenderer(
                    $result->getUnits(),
                    $this->getCacheWarmingListRenderer($isReset)
                );
                $renderer->setDependencies($result->getDependencies());
                $renderer->setPharMode($isPhar);
            }
            elseif ($isStatic === TRUE)
            {
                $renderer = new StaticRenderer(
                    $result->getUnits(),
                    $this->getStaticRequireListRenderer($isOnce)
                );
                $renderer->setDependencies($result->getDependencies());
                $renderer->setPharMode($isPhar);
            }
            else
            {
                $renderer = new AutoloadRenderer($result->getUnits());

                if ($this->config->usePrepend())
                {
                    $renderer->prependAutoloader();
                }

                if ($this->config->useExceptions())
                {
                    $renderer->enableExceptions();
                }
            }

            $renderer->setCompat($isCompat);

            $basedir = $this->config->getBaseDirectory();
            if (!$basedir || !is_dir($basedir))
            {
                throw new RuntimeException("Given basedir '{$basedir}' does not exist or is not a directory");
            }
            $renderer->setBaseDir($basedir);

            $format = $this->config->getDateFormat();
            if ($format)
            {
                $renderer->setDateTimeFormat($format);
            }

            $renderer->setIndent($this->config->getIndent());
            $renderer->setLineBreak($this->config->getLinebreak());

            foreach($this->config->getVariables() as $name => $value)
            {
                $renderer->setVariable($name, $value);
            }

            return $renderer;
        }

        private function getStaticRequireListRenderer($useOnce)
        {
            return new StaticRequireListRenderer(
                $useOnce,
                $this->config->getIndent(),
                $this->config->getLinebreak()
            );
        }

        private function getCacheWarmingListRenderer($addReset)
        {
            return new CacheWarmingListRenderer(
                $addReset,
                $this->config->getIndent(),
                $this->config->getLinebreak()
            );
        }

    }