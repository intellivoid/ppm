<?php

    declare(strict_types=1);

    namespace ppm\Traits;


    trait SmartObject
    {

        /**
         * @throws MemberAccessException
         */
        public function __call(string $name, array $args)
        {
            $class = get_class($this);

            if (ObjectHelpers::hasProperty($class, $name) === 'event') { // calling event handlers
                $handlers = $this->$name ?? null;
                if (is_iterable($handlers)) {
                    foreach ($handlers as $handler) {
                        $handler(...$args);
                    }
                } elseif ($handlers !== null) {
                    throw new UnexpectedValueException("Property $class::$$name must be iterable or null, " . gettype($handlers) . ' given.');
                }

            } else {
                ObjectHelpers::strictCall($class, $name);
            }
        }

    }