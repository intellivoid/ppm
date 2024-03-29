<?php

    declare(strict_types=1);

    namespace ppm\Traits;


    use ppm\Exceptions\MemberAccessException;
    use ppm\Exceptions\UnexpectedValueException;
    use ppm\Utilities\ObjectHelpers;
    use ReflectionException;

    /**
     * Trait SmartObject
     * @package ppm\Traits
     */
    trait SmartObject
    {

        /**
         * @param string $name
         * @param array $args
         * @throws ReflectionException
         */
        public function __call(string $name, array $args)
        {
            $class = get_class($this);

            if (ObjectHelpers::hasProperty($class, $name) === 'event')
            { // calling event handlers
                $handlers = $this->$name ?? null;
                if (is_iterable($handlers))
                {
                    foreach ($handlers as $handler)
                    {
                        $handler(...$args);
                    }
                }
                elseif ($handlers !== null)
                {
                    throw new UnexpectedValueException("Property $class::$$name must be iterable or null, " . gettype($handlers) . ' given.');
                }

            }
            else
            {
                ObjectHelpers::strictCall($class, $name);
            }
        }

        /**
         * @param string $name
         * @param array $args
         * @throws ReflectionException
         */
        public static function __callStatic(string $name, array $args)
        {
            ObjectHelpers::strictStaticCall(static::class, $name);
        }

        /**
         * @param string $name
         * @return mixed
         * @throws ReflectionException
         */
        public function &__get(string $name)
        {
            $class = get_class($this);

            if ($prop = ObjectHelpers::getMagicProperties($class)[$name] ?? null)
            { // property getter
                if (!($prop & 0b0001))
                {
                    throw new MemberAccessException("Cannot read a write-only property $class::\$$name.");
                }
                $m = ($prop & 0b0010 ? 'get' : 'is') . $name;
                if ($prop & 0b0100)
                { // return by reference
                    return $this->$m();
                }
                else
                {
                    $val = $this->$m();
                    return $val;
                }
            }
            else
            {
                ObjectHelpers::strictGet($class, $name);
            }
        }

        /**
         * @param string $name
         * @param $value
         * @throws ReflectionException
         */
        public function __set(string $name, $value)
        {
            $class = get_class($this);

            if (ObjectHelpers::hasProperty($class, $name))
            { // unsetted property
                $this->$name = $value;

            }
            elseif ($prop = ObjectHelpers::getMagicProperties($class)[$name] ?? null)
            { // property setter
                if (!($prop & 0b1000))
                {
                    throw new MemberAccessException("Cannot write to a read-only property $class::\$$name.");
                }
                $this->{'set' . $name}($value);

            }
            else
            {
                ObjectHelpers::strictSet($class, $name);
            }
        }

        /**
         * @param string $name
         */
        public function __unset(string $name)
        {
            $class = get_class($this);
            if (!ObjectHelpers::hasProperty($class, $name))
            {
                throw new MemberAccessException("Cannot unset the property $class::\$$name.");
            }
        }

        /**
         * @param string $name
         * @return bool
         * @throws ReflectionException
         */
        public function __isset(string $name): bool
        {
            return isset(ObjectHelpers::getMagicProperties(get_class($this))[$name]);
        }
    }