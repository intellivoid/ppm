<?php

    namespace ppm\Classes\DirectoryScanner;

    use FilterIterator;

    /**
     * FilterIterator to accept on files from a directory iterator
     */
    class FilesOnlyFilterIterator extends FilterIterator
    {

        /**
         * FilterIterator Method to decide whether or not to include
         * the current item into the list
         *
         * @return boolean
         */
        public function accept()
        {
            switch($this->current()->getType())
            {
                case 'file':
                    return true;

                case 'link':
                    return is_file(realpath($this->current()->getPathname()));

                default:
                    return false;
            }
        }

    }

