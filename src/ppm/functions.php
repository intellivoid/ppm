<?php

    use ppm\ppm;

    /**
     * @param string $package
     * @param string $version
     * @return bool
     */
    function ppm_import(string $package, string $version="latest"): bool
    {
        return ppm::import($package, $version);
    }