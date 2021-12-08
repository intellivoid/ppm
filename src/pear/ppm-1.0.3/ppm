<?php

    /**
     * Version 1.0.0.3
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

        // Modify the include path
        if(function_exists('get_include_path') && function_exists('set_include_path'))
        {
            if(file_exists(PPM_INCLUDE_PATH))
            {
                $include_paths = explode(':', get_include_path());
                if(in_array(PPM_INCLUDE_PATH, $include_paths) == false)
                    $include_paths[] = PPM_INCLUDE_PATH;
                set_include_path(implode(':', $include_paths));
            }
        }
        else
        {
            trigger_error('PPM Cannot modify the include path during runtime because the functions \'get_include_path()\' and \'set_include_path()\' is not enabled, is the environment compiled correctly?', E_USER_WARNING);
        }

        /** @noinspection PhpIncludeInspection */
        require_once($TargetInstall);
    }