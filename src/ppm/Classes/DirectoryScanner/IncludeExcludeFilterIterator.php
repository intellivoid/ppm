<?php

    namespace ppm\Classes\DirectoryScanner;

    use FilterIterator;

    /**
     * FilterIterator to accept Items based on include/exclude conditions
     */
    class IncludeExcludeFilterIterator extends FilterIterator
    {

        /**
         * List of filter for include shell patterns
         *
         * @var array
         */
        protected $include;

        /**
         * List of filter for exclude shell patterns
         *
         * @var array
         */
        protected $exclude;

        /**
         * Set and by that overwrite the include filter array
         *
         * @param array $inc Array of include pattern strings
         *
         * @return void
         */
        public function setInclude(array $inc = array())
        {
            $this->include = $inc;
        }

        /**
         * Set and by that overwrite the exclude filter array
         *
         * @param array $exc Array of exclude pattern strings
         *
         * @return void
         */
        public function setExclude(array $exc = array())
        {
            $this->exclude = $exc;
        }

        /**
         * FilterIterator Method to decide whether or not to include
         * the current item into the list
         *
         * @return boolean
         */
        public function accept()
        {
            $pathname = $this->current()->getPathname();

            foreach($this->exclude as $out)
            {
                if (fnmatch($out, $pathname))
                {
                    return false;
                }
            }

            foreach($this->include as $in)
            {
                if (fnmatch($in, $pathname))
                {
                    return true;
                }
            }

            return false;
        }

    }