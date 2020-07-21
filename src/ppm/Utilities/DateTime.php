<?php

    declare(strict_types=1);

    namespace ppm\Utilities;

    use DateTimeInterface;
    use DateTimeZone;
    use Exception;
    use JsonSerializable;

    /**
     * Class DateTime
     * @package ppm\Utilities
     */
    class DateTime extends \DateTime implements JsonSerializable
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

        /**
         * DateTime object factory.
         *
         * @param $time
         * @return DateTime|static
         * @throws Exception
         */
        public static function from($time)
        {
            if ($time instanceof DateTimeInterface)
            {
                return new static($time->format('Y-m-d H:i:s.u'), $time->getTimezone());

            }
            elseif (is_numeric($time))
            {
                if ($time <= self::YEAR)
                {
                    $time += time();
                }
                return (new static('@' . $time))->setTimezone(new DateTimeZone(date_default_timezone_get()));

            }
            else
            {
                return new static((string) $time);
            }
        }
    }