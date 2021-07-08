<?php

    /**
     * Loads PPM and all of it's components into memory, this is ideal for the CLI application usage when
     * compiling or managing packages but can cause performance issues during runtime when running programs
     * managed by PPM.
     */
    function ppm_load_full()
    {
        if(defined("PPM_ENVIRONMENT"))
            return;

        require_once(__DIR__ . DIRECTORY_SEPARATOR . "runtime.php");
        require_once(__DIR__ . DIRECTORY_SEPARATOR . "full.php");
        require_once(__DIR__ . DIRECTORY_SEPARATOR . "definitions.php");

        define("PPM_ENVIRONMENT", "FULL");
    }

    /**
     * Loads only the required components for PPM into memory, ideal for runtime environments where the
     * programs managed by PPM is executed repeatedly.
     */
    function ppm_load_runtime()
    {
        if(defined("PPM_ENVIRONMENT"))
            return;

        require_once(__DIR__ . DIRECTORY_SEPARATOR . "runtime.php");
        require_once(__DIR__ . DIRECTORY_SEPARATOR . "definitions.php");

        define("PPM_ENVIRONMENT", "OPTIMIZED");
    }