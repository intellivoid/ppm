<?php

    declare(strict_types=1);

    namespace ppm\Utilities;

    use ppm\Exceptions\MemberAccessException;
    use ReflectionClass;
    use ReflectionException;
    use ReflectionMethod;
    use ReflectionProperty;
    use Reflector;

    /**
     * Class ObjectHelpers
     * @package ppm\Utilities
     */
    final class ObjectHelpers
    {
        /**
         * @param string $class
         * @param string $name
         * @throws ReflectionException
         * @noinspection PhpUnused
         */
        public static function strictGet(string $class, string $name): void
        {
            $rc = new ReflectionClass($class);
            $hint = self::getSuggestion(array_merge(
                array_filter($rc->getProperties(ReflectionProperty::IS_PUBLIC), function ($p) { return !$p->isStatic(); }),
                self::parseFullDoc($rc, '~^[ \t*]*@property(?:-read)?[ \t]+(?:\S+[ \t]+)??\$(\w+)~m')
            ), $name);
            throw new MemberAccessException("Cannot read an undeclared property $class::\$$name" . ($hint ? ", did you mean \$$hint?" : '.'));
        }

        /**
         * @param string $class
         * @param string $name
         * @throws ReflectionException
         * @noinspection PhpUnused
         */
        public static function strictSet(string $class, string $name): void
        {
            $rc = new ReflectionClass($class);
            $hint = self::getSuggestion(array_merge(
                array_filter($rc->getProperties(ReflectionProperty::IS_PUBLIC), function ($p) { return !$p->isStatic(); }),
                self::parseFullDoc($rc, '~^[ \t*]*@property(?:-write)?[ \t]+(?:\S+[ \t]+)??\$(\w+)~m')
            ), $name);
            throw new MemberAccessException("Cannot write to an undeclared property $class::\$$name" . ($hint ? ", did you mean \$$hint?" : '.'));
        }

        /**
         * @param string $class
         * @param string $method
         * @param array $additionalMethods
         * @throws ReflectionException
         * @noinspection PhpUnused
         */
        public static function strictCall(string $class, string $method, array $additionalMethods = []): void
        {
            $hint = self::getSuggestion(array_merge(
                get_class_methods($class),
                self::parseFullDoc(new ReflectionClass($class), '~^[ \t*]*@method[ \t]+(?:\S+[ \t]+)??(\w+)\(~m'),
                $additionalMethods
            ), $method);

            if (method_exists($class, $method)) { // called parent::$method()
                $class = 'parent';
            }
            throw new MemberAccessException("Call to undefined method $class::$method()" . ($hint ? ", did you mean $hint()?" : '.'));
        }

    }