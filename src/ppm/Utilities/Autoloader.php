<?php


    namespace ppm\Utilities;


    use Exception;
    use ppm\Exceptions\AutoloaderException;
    use ppm\Exceptions\InvalidComponentException;
    use ppm\Objects\Package\Component;
    use ZiProto\ZiProto;

    /**
     * Class Autoloader
     * @package ppm\Utilities
     */
    class Autoloader
    {
        /**
         * @param string $package_path
         * @return bool
         * @throws AutoloaderException
         */
        public static function loadStaticLoader(string $package_path): bool
        {
            $autoloader = $package_path . DIRECTORY_SEPARATOR . '.ppm' . DIRECTORY_SEPARATOR . 'COMPONENTS';

            if(file_exists($autoloader) == false)
            {
                throw new AutoloaderException("The file '$autoloader' was not found");
            }

            try
            {
                $Autoloader = ZiProto::decode(file_get_contents($autoloader));
            }
            catch(Exception $exception)
            {
                throw new AutoloaderException("The file '$autoloader' could not be loaded, the data may be corrupted");
            }

            foreach($Autoloader as $component)
            {
                $path = $package_path . DIRECTORY_SEPARATOR . str_ireplace("::", DIRECTORY_SEPARATOR, $component);

                if(file_exists($path) == false)
                {
                    throw new AutoloaderException("A required component '" . $component . "' was not found in '" . $package_path . "'");
                }
                else
                {
                    /** @noinspection PhpIncludeInspection */
                    include_once($path);
                }
            }

            return true;
        }
    }