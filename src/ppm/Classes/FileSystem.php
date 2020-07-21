<?php


    namespace ppm\Classes;

    use ppm\Exceptions\IOException;

    final class FileSystem
    {
        /**
         * Creates a directory.
         * @throws IOException
         */
        public static function createDir(string $dir, int $mode = 0777): void
        {
            if (!is_dir($dir) && !@mkdir($dir, $mode, true) && !is_dir($dir)) { // @ - dir may already exist
                throw new IOException("Unable to create directory '$dir' with mode " . decoct($mode) . '. ' . Helpers::getLastError());
            }
        }
    }