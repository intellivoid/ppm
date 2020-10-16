<?php


    namespace ppm\Classes\Composer\Service;


    /**
     * Class IsThisEmpty
     * An abstract class that provides an 'isEmpty()' method.
     * @package ppm\Classes\Composer\Service
     */
    abstract class IsThisEmpty
    {
        /**
         * Checks if the current instance is empty by evaluating all properties.
         * @return bool
         */
        public function isEmpty()
        {
            foreach ($this as $prop => $value)
            {
                if (!empty($this->$prop))
                {
                    return false;
                }
            }

            return true;
        }
    }
