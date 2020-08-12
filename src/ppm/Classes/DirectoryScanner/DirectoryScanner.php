<?php /** @noinspection PhpUnused */

    namespace ppm\Classes\DirectoryScanner;

    use DirectoryIterator;
    use FilesystemIterator;
    use Iterator;
    use RecursiveDirectoryIterator;
    use RecursiveIteratorIterator;

    /**
     * Recursive scanner for files on given filesystem path with the ability to filter
     * results based on include and exclude patterns
     */
    class DirectoryScanner
    {

        /**
         * List of filter for include shell patterns
         *
         * @var array
         */
        protected $include = array();

        /**
         * List of filter for exclude shell patterns
         *
         * @var array
         */
        protected $exclude = array();

        /**
         * Flags to pass on to RecursiveDirectoryIterator on construction
         *
         * @var int
         */
        protected $flags = 0;

        /**
         * Add a new pattern to the include array
         *
         * @param string $inc Pattern to add
         *
         * @return void
         * @noinspection PhpUnused
         */
        public function addInclude($inc)
        {
            $this->include[] = $inc;
        }

        /**
         * set the include pattern array
         *
         * @param array $inc Array of include pattern strings
         *
         * @return void
         * @noinspection PhpUnused
         */
        public function setIncludes(array $inc = array())
        {
            $this->include = $inc;
        }

        /**
         * get array of current include patterns
         *
         * @return array
         * @noinspection PhpUnused
         */
        public function getIncludes()
        {
            return $this->include;
        }

        /**
         * @param $flag
         * @throws Exception
         */
        public function setFlag($flag)
        {
            if (!$this->isValidFlag($flag))
            {
                throw new Exception("Invalid flag specified", Exception::InvalidFlag);
            }
            $this->flags = $this->flags | $flag;
        }

        /**
         * @param $flag
         * @throws Exception
         */
        public function unsetFlag($flag)
        {
            if (!$this->isValidFlag($flag))
            {
                throw new Exception("Invalid flag specified", Exception::InvalidFlag);
            }
            $this->flags = $this->flags & ~$flag;
        }

        /**
         * @param boolean $followSymlinks
         *
         * @return void
         * @throws Exception
         * @throws Exception
         * @deprecated Use setFlag / unsetFlag with \FilesystemIterator::FOLLOW_SYMLINKS
         * @noinspection PhpUnused
         */
        public function setFollowSymlinks($followSymlinks)
        {
            if ($followSymlinks == true)
            {
                $this->setFlag(FilesystemIterator::FOLLOW_SYMLINKS);
                return;
            }
            $this->unsetFlag(FilesystemIterator::FOLLOW_SYMLINKS);
        }

        /**
         * Public function, so it can be tested properly
         *
         * @return bool
         * @noinspection PhpUnused
         */
        public function isFollowSymlinks()
        {
            return ($this->flags & FilesystemIterator::FOLLOW_SYMLINKS) == FilesystemIterator::FOLLOW_SYMLINKS;
        }

        /**
         * Add a new pattern to the exclude array
         *
         * @param string $exc Pattern to add
         * @return void
         * @noinspection PhpUnused
         */
        public function addExclude($exc)
        {
            $this->exclude[] = $exc;
        }

        /**
         * set the exclude pattern array
         *
         * @param array $exc Array of exclude pattern strings
         * @return void
         * @noinspection PhpUnused
         */
        public function setExcludes(array $exc = array())
        {
            $this->exclude = $exc;
        }

        /**
         * get array of current exclude patterns
         *
         * @return array
         * @noinspection PhpUnused
         */
        public function getExcludes()
        {
            return $this->exclude;
        }

        /**
         * get an array of splFileObjects from given path matching the
         * include/exclude patterns
         *
         * @param string $path Path to work on
         * @param boolean $recursive Scan recursivly or not
         *
         * @return array of splFileInfo Objects
         * @throws Exception
         * @throws Exception
         * @noinspection PhpUnused
         */
        public function getFiles($path, $recursive = true)
        {
            $res = array();
            foreach($this->getIterator($path, $recursive) as $entry)
            {
                $res[] = $entry;
            }
            return $res;
        }

        /**
         * Magic invoker method to use object in foreach-alike constructs as iterator,
         * delegating work to getIterator() method
         *
         * @param string $path Path to work on
         * @param boolean $recursive Scan recursivly or not
         *
         * @return Iterator
         * @throws Exception
         * @throws Exception
         * @see getIterator
         *
         */
        public function __invoke($path, $recursive = true)
        {
            return $this->getIterator($path, $recursive);
        }

        /**
         * Scan given directory for files, returning splFileObjects matching the include/exclude patterns
         *
         * @param string $path Path to work on
         * @param boolean $recursive Scan recursively or not
         *
         * @return IncludeExcludeFilterIterator
         * @throws Exception
         */
        public function getIterator($path, $recursive = true)
        {
            if (!file_exists($path))
            {
                throw new Exception("Path '$path' does not exist.", Exception::NotFound);
            }
            if ($recursive)
            {
                $worker = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, $this->flags));
            }
            else
            {
                $worker = new DirectoryIterator($path);
            }
            $filter = new IncludeExcludeFilterIterator( new FilesOnlyFilterIterator($worker) );
            $filter->setInclude( count($this->include) ? $this->include : array('*'));
            $filter->setExclude($this->exclude);
            return $filter;
        }

        /**
         * @param $flag
         * @return bool
         */
        protected function isValidFlag($flag)
        {
            return in_array($flag, array(
                FilesystemIterator::CURRENT_AS_PATHNAME,
                FilesystemIterator::CURRENT_AS_FILEINFO,
                FilesystemIterator::CURRENT_AS_SELF,
                FilesystemIterator::CURRENT_MODE_MASK,
                FilesystemIterator::KEY_AS_PATHNAME,
                FilesystemIterator::KEY_AS_FILENAME,
                FilesystemIterator::FOLLOW_SYMLINKS,
                FilesystemIterator::KEY_MODE_MASK,
                FilesystemIterator::NEW_CURRENT_AND_KEY,
                FilesystemIterator::SKIP_DOTS,
                FilesystemIterator::UNIX_PATHS
            ));
        }

    }