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

        /**
         * Determines if the directory can be written to
         *
         * @param $path
         * @return bool
         */
        public static function writeTest($path): bool
        {
            if(file_exists($path) == false)
            {
                return false;
            }

            if(is_writeable($path) == false)
            {
                return false;
            }

            $write_test_file = $path . DIRECTORY_SEPARATOR . 'write_test';

            if(file_exists($write_test_file))
            {
                if(@unlink($write_test_file) !== true)
                {
                    return false;
                }
            }

            file_put_contents($write_test_file, "0");

            if(file_exists($write_test_file) == false)
            {
                return false;
            }

            unlink($write_test_file);
            return true;
        }
    }