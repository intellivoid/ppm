<?php

    declare(strict_types=1);

    namespace ppm\Utilities;

    /**
     * Class DateTime
     * @package ppm\Utilities
     */
    class DateTime extends \DateTime implements \JsonSerializable
    {
        /** minute in seconds */
        public const MINUTE = 60;

        /** hour in seconds */
        public const HOUR = 60 * self::MINUTE;

        /** day in seconds */
        public const DAY = 24 * self::HOUR;

        /** week in seconds */
        public const WEEK = 7 * self::DAY;

        /** average month in seconds */
        public const MONTH = 2629800;

        /** average year in seconds */
        public const YEAR = 31557600;

        
    }