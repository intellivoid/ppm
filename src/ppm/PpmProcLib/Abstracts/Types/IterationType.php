<?php


    namespace PpmProcLib\Abstracts\Types;

    /**
     * Class IterationType
     * @package ProcLib\Abstracts\Types
     */
    abstract class IterationType
    {
        public const ITER_NON_BLOCKING = 1; // By default, iterating over outputs is a blocking call, use this flag to make it non-blocking
        public const ITER_KEEP_OUTPUT = 2;  // By default, outputs are cleared while iterating, use this flag to keep them in memory
        public const ITER_SKIP_OUT = 4;     // Use this flag to skip STDOUT while iterating
        public const ITER_SKIP_ERR = 8;     // Use this flag to skip STDERR while iterating
    }