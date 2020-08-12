<?php /** @noinspection PhpUnused */

    namespace ppm\Classes\DirectoryScanner;

    use FilterIterator;
    use finfo;

    /**
     * FilterIterator to accept only php source files based on content
     */
    class PHPFilterIterator extends FilterIterator
    {

        /**
         * FilterIterator Method to decide whether or not to include
         * the current item into the list
         *
         * @return boolean
         */
        public function accept()
        {
            $finfo = new finfo(FILEINFO_MIME);
            return strpos($finfo->file($this->current()->getPathname()), 'text/x-php') === 0;
        }

    }
