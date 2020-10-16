<?php


    namespace ppm\Classes\Composer\Service;


    use function property_exists;

    /**
     * An improved standard class.
     *
     * Class AbstractClass
     * @package ppm\Classes\Composer\Service
     */
    abstract class AbstractClass extends IsThisEmpty
    {
        /**
         * @see http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
         * @param string $name
         * @return null|string
         */
        public function __get(string $name)
        {
            if (property_exists($this, $name))
            {
                return $this->$name;
            }

            return null;
        }


        /**
         * @see http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
         * @param string $name
         * @return bool
         */
        public function __isset(string $name): bool
        {
            return (property_exists($this, $name) && !empty($this->$name));
        }
    }