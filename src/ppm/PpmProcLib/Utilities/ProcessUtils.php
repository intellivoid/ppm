<?php


    namespace PpmProcLib\Utilities;

    use Generator;
    use Iterator;
    use IteratorIterator;
    use PpmProcLib\Exceptions\InvalidArgumentException;
    use PpmProcLib\Process;
    use Traversable;
    use function is_resource;
    use function is_string;

    /**
     * Class ProcessUtils
     * @package ProcLib\Utilities
     */
    class ProcessUtils
    {
        /**
         * This class should not be instantiated.
         */
        private function __construct()
        {
        }

        /**
         * Validates and normalizes a Process input.
         *
         * @param string $caller The name of method call that validates the input
         * @param mixed $input The input to validate
         *
         * @return Generator|Iterator|IteratorIterator|resource|string|null The validated input
         *
         * @throws InvalidArgumentException In case the input is not valid
         * @noinspection PhpMissingReturnTypeInspection
         * @noinspection PhpMissingParamTypeInspection
         */
        public static function validateInput(string $caller, $input)
        {
            if (null !== $input)
            {
                if (is_resource($input))
                {
                    return $input;
                }

                if (is_string($input))
                {
                    return $input;
                }

                if (is_scalar($input))
                {
                    return (string) $input;
                }

                if ($input instanceof Process)
                {
                    return $input->getIterator($input::ITER_SKIP_ERR);
                }

                if ($input instanceof Iterator)
                {
                    return $input;
                }

                if ($input instanceof Traversable)
                {
                    return new IteratorIterator($input);
                }

                throw new InvalidArgumentException(sprintf('"%s" only accepts strings, Traversable objects or stream resources.', $caller));
            }

            /** @noinspection PhpExpressionAlwaysNullInspection */
            return $input;
        }
    }
