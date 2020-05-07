<?php


    namespace ppm\Utilities;


    /**
     * Class IO
     * @package ppm\Utilities
     */
    class IO
    {
        /**
         * @param $dir
         */
        public static function deleteDirectory($dir)
        {
            if (is_dir($dir))
            {
                $objects = scandir($dir);
                foreach ($objects as $object)
                {
                    if ($object != "." && $object != "..")
                    {
                        if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
                        {
                            self::deleteDirectory($dir. DIRECTORY_SEPARATOR .$object);
                        }
                        else
                        {
                            unlink($dir. DIRECTORY_SEPARATOR .$object);
                        }
                    }
                }
                rmdir($dir);
            }
        }
    }