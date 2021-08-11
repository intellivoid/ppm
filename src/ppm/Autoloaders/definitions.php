<?php

    /**
     * This autoloader defines the PPM definitions into the environment if not already available, this requires PPM
     * to either be fully loaded or partially loaded for this to work.
     */

    use ppm\Utilities\PathFinder;

    if(defined('PPM') == false)
    {
        $ppm_info_path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'ppm.json';

        if(file_exists($ppm_info_path) == false)
        {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new Exception("The file '$ppm_info_path' does not exist" , E_USER_WARNING);
        }
        else
        {
            $ppm_info = json_decode(file_get_contents($ppm_info_path), true);
            define('PPM_VERSION', $ppm_info['VERSION']);
            define('PPM_AUTHOR', $ppm_info['AUTHOR']);
            define('PPM_URL', $ppm_info['URL']);
            define('PPM_ORGANIZATION', $ppm_info['ORGANIZATION']);
            define('PPM_STATE', $ppm_info['STATE']);
        }

        define('PPM', true);
        define('PPM_INSTALL', __DIR__);
        define('PPM_DATA', PathFinder::getMainPath(false));
    }