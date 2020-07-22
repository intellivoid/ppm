<?php

    declare(strict_types=1);

    namespace ppm\Classes;

    /**
     * Class Json
     * @package ppm\Classes
     */
    final class Json
    {
        public const FORCE_ARRAY = 0b0001;

        public const PRETTY = 0b0010;

        public const ESCAPE_UNICODE = 0b0100;
    }