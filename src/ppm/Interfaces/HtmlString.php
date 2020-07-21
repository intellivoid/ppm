<?php
    /** @noinspection PhpUnused */

    declare(strict_types=1);


    namespace ppm\Interfaces;


    /**
     * Interface HtmlString
     * @package ppm\Interfaces
     */
    interface HtmlString
    {
        /**
         * Returns string in HTML format
         */
        function __toString(): string;
    }
