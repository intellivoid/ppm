<?php


    namespace PpmProcLib\Utilities;

    use Iterator;
    use IteratorAggregate;
    use PpmProcLib\Exceptions\RuntimeException;
    use Traversable;

    /**
     * Class InputStream
     * @package ProcLib\Utilities
     */
    class InputStream implements IteratorAggregate
    {
        /**
         * @var null|callable
         */
        private $onEmpty = null;

        /**
         * @var array
         */
        private $input = [];

        /**
         * @var bool
         */
        private $open = true;

        /**
         * Sets a callback that is called when the write buffer becomes empty.
         *
         * @param callable|null $onEmpty
         */
        public function onEmpty(callable $onEmpty = null)
        {
            $this->onEmpty = $onEmpty;
        }

        /**
         * Appends an input to the write buffer.
         *
         * @param resource|string|int|float|bool|Traversable|null $input The input to append as scalar,
         *                                                                stream resource or \Traversable
         */
        public function write($input)
        {
            if (null === $input)
            {
                return;
            }

            if ($this->isClosed())
            {
                throw new RuntimeException(sprintf('"%s" is closed.', static::class));
            }

            $this->input[] = ProcessUtils::validateInput(__METHOD__, $input);
        }

        /**
         * Closes the write buffer.
         */
        public function close()
        {
            $this->open = false;
        }

        /**
         * Tells whether the write buffer is closed or not.
         */
        public function isClosed()
        {
            return !$this->open;
        }

        /**
         * @return Traversable
         */
        public function getIterator()
        {
            $this->open = true;

            while ($this->open || $this->input) {
                if (!$this->input) {
                    yield '';
                    continue;
                }
                $current = array_shift($this->input);

                if ($current instanceof Iterator) {
                    yield from $current;
                } else {
                    yield $current;
                }
                if (!$this->input && $this->open && null !== $onEmpty = $this->onEmpty) {
                    $this->write($onEmpty($this));
                }
            }
        }
    }
