<?php


    namespace PpmProcLib\Abstracts\Types;

    /**
     * Class StatusType
     * @package ProcLib\Abstracts\Types
     */
    abstract class StatusType
    {
        public const STATUS_READY = 'ready';
        public const STATUS_STARTED = 'started';
        public const STATUS_TERMINATED = 'terminated';
    }