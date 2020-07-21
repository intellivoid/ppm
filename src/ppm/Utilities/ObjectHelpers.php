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


    }