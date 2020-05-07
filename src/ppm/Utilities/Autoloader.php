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
         * @throws InvalidComponentException
         */
        public static function loadStaticLoader(string $package_path): bool
        {
            $autoloader = $package_path . DIRECTORY_SEPARATOR . 'autoload.static.ppm';

            if(file_exists($package_path . $autoloader) == false)
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
                $component = Component::fromArray($component, $package_path);

                if(file_exists($component->getPath()) == false)
                {
                    if($component->Required)
                    {
                        throw new AutoloaderException("A required component '" . $component->File . "' was not found in '" . $component->getPath() . "'");
                    }
                }
                else
                {
                    // TODO: Check if the package was already imported to speed up the process
                    include_once($component->getPath());
                }
            }

            return true;
        }
    }