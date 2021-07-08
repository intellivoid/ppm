<?php

    /**
     * Version 1.0.0.2
     *
     * This script attempts to find and load PPM (PHP Package Manager) allowing your stand-alone
     * software to import PPM packages without needing to build one yourself.
     */

    if(class_exists("ppm\ppm") == false)
    {
        $LocalInstall = __DIR__ . DIRECTORY_SEPARATOR .'ppm';
        $SystemInstall = null;

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
        {
            throw new RuntimeException("PPM Cannot run on a Windows Environment");
        }
        else
        {
            $SystemInstall = realpath(DIRECTORY_SEPARATOR . "var");
        }

        $SystemInstall = $SystemInstall .  DIRECTORY_SEPARATOR . "ppm";
        $TargetInstall = null;

        if(file_exists($LocalInstall . DIRECTORY_SEPARATOR . "ppm.php"))
        {
            $TargetInstall = $LocalInstall . DIRECTORY_SEPARATOR . "ppm.php";

            /** @noinspection PhpIncludeInspection */
            require_once($LocalInstall . DIRECTORY_SEPARATOR . "Autoloaders" . DIRECTORY_SEPARATOR . "main.php");
        }
        elseif(file_exists($SystemInstall . DIRECTORY_SEPARATOR . "ppm.php"))
        {
            $TargetInstall = $SystemInstall . DIRECTORY_SEPARATOR . "ppm.php";

            /** @noinspection PhpIncludeInspection */
            require_once($SystemInstall . DIRECTORY_SEPARATOR . "Autoloaders" . DIRECTORY_SEPARATOR . "main.php");
        }
        else
        {
            throw new RuntimeException("The PPM install cannot be located on the system");
        }

        if(function_exists("ppm_load_runtime") == false)
            throw new RuntimeException("PPM Cannot be loaded into the runtime environment due to a missing function");

        // Load PPM partially
        ppm_load_runtime();

        /** @noinspection PhpIncludeInspection */
        require_once($TargetInstall);
    }