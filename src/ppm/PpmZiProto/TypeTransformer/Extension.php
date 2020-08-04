<?php

    namespace PpmZiProto\TypeTransformer;

    use PpmZiProto\BufferStream;

    /**
     * Interface Extension
     * @package ZiProto\TypeTransformer
     */
    interface Extension
    {
        /**
         * @return int
         */
        public function getType() : int;

        /**
         * @param BufferStream $stream
         * @param int $extLength
         * @return mixed
         */
        public function decode(BufferStream $stream, int $extLength);
    }