<?php

    use ppm\Exceptions\AutoloaderException;
    use ppm\Exceptions\InvalidComponentException;
    use ppm\Exceptions\InvalidPackageLockException;
    use ppm\Exceptions\PackageNotFoundException;
    use ppm\Exceptions\VersionNotFoundException;
    use ppm\ppm;

    /**
     * Imports a package using PPM
     *
     * @param string $package
     * @param string $version
     * @param bool $import_dependencies
     * @param bool $throw_error
     * @return bool
     * @throws AutoloaderException
     * @throws InvalidComponentException
     * @throws InvalidPackageLockException
     * @throws PackageNotFoundException
     * @throws VersionNotFoundException
     */
    function ppm_import(string $package, string $version="latest", bool $import_dependencies=true, bool $throw_error=true): bool
    {
        return ppm::import($package, $version, $import_dependencies, $throw_error);
    }

    if(function_exists("import") == false)
    {
        /**
         * Imports a package using PPM
         *
         * @param string $package
         * @param string $version
         * @param bool $import_dependencies
         * @param bool $throw_error
         * @return bool
         * @throws AutoloaderException
         * @throws InvalidComponentException
         * @throws InvalidPackageLockException
         * @throws PackageNotFoundException
         * @throws VersionNotFoundException
         */
        function import(string $package, string $version="latest", bool $import_dependencies=true, bool $throw_error=true): bool
        {
            return ppm::import($package, $version, $import_dependencies, $throw_error);
        }
    }

    /**
     * Returns the definitions defined by PPM
     *
     * @return array
     * @noinspection PhpUnused
     */
    function ppm_definitions(): array
    {
        return array(
            "PPM_VERSION" => PPM_VERSION,
            "PPM_AUTHOR" => PPM_AUTHOR,
            "PPM_URL" => PPM_URL,
            "PPM_STATE" => PPM_STATE,
            "PPM_INSTALL" => PPM_INSTALL,
            "PPM_DATA" => PPM_DATA
        );
    }

