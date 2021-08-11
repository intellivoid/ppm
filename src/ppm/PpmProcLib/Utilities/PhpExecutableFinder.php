<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace PpmProcLib\Utilities;

    use function in_array;
    use const DIRECTORY_SEPARATOR;
    use const PHP_BINARY;
    use const PHP_BINDIR;
    use const PHP_EOL;
    use const PHP_SAPI;

    /**
     * Class PhpExecutableFinder
     * @package ProcLib\Utilities
     */
    class PhpExecutableFinder
    {
        /**
         * @var ExecutableFinder
         */
        private $executableFinder;

        /**
         * PhpExecutableFinder constructor.
         */
        public function __construct()
        {
            $this->executableFinder = new ExecutableFinder();
        }

        /**
         * Finds The PHP executable.
         *
         * @return string|false The PHP executable path or false if it cannot be found
         */
        public function find(bool $includeArgs = true)
        {
            if ($php = getenv('PHP_BINARY'))
            {
                if (!is_executable($php))
                {
                    $command = '\\' === DIRECTORY_SEPARATOR ? 'where' : 'command -v';
                    if ($php = strtok(exec($command.' '.escapeshellarg($php)), PHP_EOL))
                    {
                        if (!is_executable($php))
                        {
                            return false;
                        }
                    }
                    else
                    {
                        return false;
                    }
                }

                return $php;
            }

            $args = $this->findArguments();
            $args = $includeArgs && $args ? ' '.implode(' ', $args) : '';

            // PHP_BINARY return the current sapi executable
            if (PHP_BINARY && in_array(PHP_SAPI, ['cgi-fcgi', 'cli', 'cli-server', 'phpdbg'], true))
            {
                return PHP_BINARY.$args;
            }

            if ($php = getenv('PHP_PATH'))
            {
                if (!@is_executable($php))
                {
                    return false;
                }

                return $php;
            }

            if ($php = getenv('PHP_PEAR_PHP_BIN'))
            {
                if (@is_executable($php))
                {
                    return $php;
                }
            }

            if (@is_executable($php = PHP_BINDIR.('\\' === DIRECTORY_SEPARATOR ? '\\php.exe' : '/php')))
            {
                return $php;
            }

            $dirs = [PHP_BINDIR];

            if ('\\' === DIRECTORY_SEPARATOR)
            {
                $dirs[] = 'C:\xampp\php\\';
            }

            return $this->executableFinder->find('php', false, $dirs);
        }

        /**
         * Finds the PHP executable arguments.
         *
         * @return array The PHP executable arguments
         */
        public function findArguments(): array
        {
            $arguments = [];
            if ('phpdbg' === PHP_SAPI)
            {
                $arguments[] = '-qrr';
            }

            return $arguments;
        }
    }