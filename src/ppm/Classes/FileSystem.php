<?php


    namespace ppm\Classes;

    use ppm\Exceptions\IOException;
    use ppm\Utilities\Helpers;

    /**
     * Class FileSystem
     * @package ppm\Classes
     */
    final class FileSystem
    {
        /**
         * Creates a directory.
         * @param string $dir
         * @param int $mode
         */
        public static function createDir(string $dir, int $mode = 0777): void
        {
            if (!is_dir($dir) && !@mkdir($dir, $mode, true) && !is_dir($dir)) { // @ - dir may already exist
                throw new IOException("Unable to create directory '$dir' with mode " . decoct($mode) . '. ' . Helpers::getLastError());
            }
        }
    }