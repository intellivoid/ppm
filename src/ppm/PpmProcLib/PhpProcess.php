<?php


    namespace PpmProcLib;

    use PpmProcLib\Exceptions\LogicException;
    use PpmProcLib\Exceptions\RuntimeException;
    use PpmProcLib\Utilities\PhpExecutableFinder;
    use const PHP_SAPI;

    /**
     * Class PhpProcess
     * @package ProcLib
     */
    class PhpProcess extends Process
    {
        /**
         * @param string $script The PHP script to run (as a string)
         * @param string|null $cwd The working directory or null to use the working dir of the current PHP process
         * @param array|null  $env The environment variables or null to use the same environment as the current PHP process
         * @param int $timeout The timeout in seconds
         * @param array|null $php Path to the PHP binary to use with any additional arguments
         */
        public function __construct(string $script, string $cwd = null, array $env = null, int $timeout = 60, array $php = null)
        {
            if (null === $php)
            {
                $executableFinder = new PhpExecutableFinder();
                $php = $executableFinder->find(false);
                $php = false === $php ? null : array_merge([$php], $executableFinder->findArguments());
            }

            if ('phpdbg' === PHP_SAPI)
            {
                $file = tempnam(sys_get_temp_dir(), 'dbg');
                file_put_contents($file, $script);
                register_shutdown_function('unlink', $file);
                $php[] = $file;
                $script = null;
            }

            parent::__construct($php, $cwd, $env, $script, $timeout);
        }

        // /**
        //  * @param string $command
        //  * @param string|null $cwd
        //  * @param array|null $env
        //  * @param null $input
        //  * @param float|int|null $timeout
        //  * @return PhpProcess|void
        //  * @inheritDoc
        //  * @noinspection PhpReturnDocTypeMismatchInspection
        //  */
        // public static function fromShellCommandline(string $command, string $cwd = null, array $env = null, $input = null, ?float $timeout = 60)
        // {
        //    return;
        //    throw new LogicException(sprintf('The "%s()" method cannot be called when using "%s".', __METHOD__, self::class));
        // }

        /**
         * @param callable|null $callback
         * @param array $env
         * @inheritDoc
         */
        public function start(callable $callback = null, array $env = [])
        {
            if (null === $this->getCommandLine())
            {
                throw new RuntimeException('Unable to find the PHP executable.');
            }

            parent::start($callback, $env);
        }
    }
