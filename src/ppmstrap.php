<?php

    /**
     * Version 1.0.0.0
     *
     * This script attempts to find and load PPM (PHP Package Manager) allowing your stand-alone
     * software to import PPM packages without needing to build one yourself.
     */

    if(class_exists("ppm\ppm") == false)
    {
        $LocalInstall = __DIR__ . DIRECTORY_SEPARATOR .'ppm' . DIRECTORY_SEPARATOR . 'ppm.php';
        $SystemInstall = null;

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
        {
            $SystemInstall = realpath(DIRECTORY_SEPARATOR);
        }
        else
        {
            $SystemInstall = realpath(DIRECTORY_SEPARATOR . "var" . DIRECTORY_SEPARATOR . "lib");
        }

        $SystemInstall = DIRECTORY_SEPARATOR . "ppm" . DIRECTORY_SEPARATOR . "ppm.php";

        if(file_exists($LocalInstall))
        {
            /** @noinspection PhpIncludeInspection */
            include_once($LocalInstall);
        }
        elseif(file_exists($SystemInstall))
        {
            /** @noinspection PhpIncludeInspection */
            include_once($SystemInstall);
        }
        else
        {
            throw new RuntimeException("The PPM install cannot be located on the system");
        }
    }