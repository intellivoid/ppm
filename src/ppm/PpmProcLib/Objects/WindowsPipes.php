<?php

    /** @noinspection PhpUnused */
    /** @noinspection PhpMissingFieldTypeInspection */

    namespace PpmProcLib\Objects;

    use BadMethodCallException;
    use PpmProcLib\Abstracts\AbstractPipes;
    use PpmProcLib\Exceptions\RuntimeException;
    use PpmProcLib\Process;
    use function strlen;
    use const LOCK_EX;
    use const LOCK_NB;
    use const LOCK_UN;

    /**
     * Class WindowsPipes
     * @package ProcLib\Pipes
     */
    class WindowsPipes extends AbstractPipes
    {
        /**
         * @var array
         */
        private $files = [];

        /**
         * @var array
         */
        private $fileHandles = [];

        /**
         * @var array
         */
        private $lockHandles = [];

        /**
         * @var int[]
         */
        private $readBytes = [
            Process::STDOUT => 0,
            Process::STDERR => 0,
        ];

        /**
         * @var bool
         */
        private $haveReadSupport;

        /**
         * WindowsPipes constructor.
         * @param $input
         * @param bool $haveReadSupport
         */
        public function __construct($input, bool $haveReadSupport)
        {
            $this->haveReadSupport = $haveReadSupport;

            if ($this->haveReadSupport)
            {
                $pipes = [
                    Process::STDOUT => Process::OUT,
                    Process::STDERR => Process::ERR,
                ];

                $tmpDir = sys_get_temp_dir();
                $lastError = 'unknown reason';
                set_error_handler(function ($type, $msg) use (&$lastError) { $lastError = $msg; });

                for ($i = 0;; ++$i)
                {
                    foreach ($pipes as $pipe => $name)
                    {
                        $file = sprintf('%s\\sf_proc_%02X.%s', $tmpDir, $i, $name);

                        if (!$h = fopen($file.'.lock', 'w'))
                        {

                            if (file_exists($file.'.lock'))
                            {
                                continue 2;
                            }

                            restore_error_handler();
                            throw new RuntimeException('A temporary file could not be opened to write the process output: ' . $lastError);
                        }

                        if (!flock($h, LOCK_EX | LOCK_NB))
                        {
                            continue 2;
                        }

                        if (isset($this->lockHandles[$pipe]))
                        {
                            flock($this->lockHandles[$pipe], LOCK_UN);
                            fclose($this->lockHandles[$pipe]);
                        }

                        $this->lockHandles[$pipe] = $h;

                        if (!($h = fopen($file, 'w')) || !fclose($h) || !$h = fopen($file, 'r'))
                        {
                            flock($this->lockHandles[$pipe], LOCK_UN);
                            fclose($this->lockHandles[$pipe]);
                            unset($this->lockHandles[$pipe]);
                            continue 2;
                        }

                        $this->fileHandles[$pipe] = $h;
                        $this->files[$pipe] = $file;
                    }
                    break;
                }
                restore_error_handler();
            }

            parent::__construct($input);
        }

        public function __sleep()
        {
            throw new BadMethodCallException('Cannot serialize '.__CLASS__);
        }

        public function __wakeup()
        {
            throw new BadMethodCallException('Cannot unserialize '.__CLASS__);
        }

        public function __destruct()
        {
            $this->close();
        }

        /**
         * {@inheritdoc}
         */
        public function getDescriptors(): array
        {
            if (!$this->haveReadSupport)
            {
                $nullstream = fopen('NUL', 'c');

                return [
                    ['pipe', 'r'],
                    $nullstream,
                    $nullstream,
                ];
            }

            // We're not using pipe on Windows platform as it hangs (https://bugs.php.net/51800)
            // We're not using file handles as it can produce corrupted output https://bugs.php.net/65650
            // So we redirect output within the commandline and pass the nul device to the process
            return [
                ['pipe', 'r'],
                ['file', 'NUL', 'w'],
                ['file', 'NUL', 'w'],
            ];
        }

        /**
         * @return array
         * @inheritDoc
         */
        public function getFiles(): array
        {
            return $this->files;
        }

        /**
         * @param bool $blocking
         * @param bool $close
         * @return array
         * @inheritDoc
         */
        public function readAndWrite(bool $blocking, bool $close = false): array
        {
            $this->unblock();
            $w = $this->write();
            $read = $r = $e = [];

            if ($blocking)
            {
                if ($w)
                {
                    @stream_select($r, $w, $e, 0, Process::TIMEOUT_PRECISION * 1E6);
                }
                elseif ($this->fileHandles)
                {
                    usleep(Process::TIMEOUT_PRECISION * 1E6);
                }
            }

            foreach ($this->fileHandles as $type => $fileHandle)
            {
                $data = stream_get_contents($fileHandle, -1, $this->readBytes[$type]);

                if (isset($data[0]))
                {
                    $this->readBytes[$type] += strlen($data);
                    $read[$type] = $data;
                }

                if ($close)
                {
                    ftruncate($fileHandle, 0);
                    fclose($fileHandle);
                    flock($this->lockHandles[$type], LOCK_UN);
                    fclose($this->lockHandles[$type]);
                    unset($this->fileHandles[$type], $this->lockHandles[$type]);
                }
            }

            return $read;
        }

        /**
         * @return bool
         * @inheritDoc
         */
        public function haveReadSupport(): bool
        {
            return $this->haveReadSupport;
        }

        /**
         * @return bool
         * @inheritDoc
         */
        public function areOpen(): bool
        {
            return $this->pipes && $this->fileHandles;
        }

        /**
         * @return void
         * @inheritDoc
         */
        public function close()
        {
            parent::close();

            foreach ($this->fileHandles as $type => $handle)
            {
                ftruncate($handle, 0);
                fclose($handle);
                flock($this->lockHandles[$type], LOCK_UN);
                fclose($this->lockHandles[$type]);
            }

            $this->fileHandles = $this->lockHandles = [];
        }
    }
