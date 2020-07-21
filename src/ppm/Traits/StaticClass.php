<?php


    namespace ppm\Traits;


    use Error;

    /**
     * Trait StaticClass
     * @package ppm\Traits
     */
    trait StaticClass
    {
        /** @throws Error */
        final public function __construct()
        {
            throw new Error('Class ' . get_class($this) . ' is static and cannot be instantiated.');
        }


        /**
         * Call to undefined static method.
         * @param string $name
         * @param array $args
         * @return void
         */
        public static function __callStatic(string $name, array $args)
        {
            //Utils\ObjectHelpers::strictStaticCall(static::class, $name);
        }
    }