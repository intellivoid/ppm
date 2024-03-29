<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace PpmProcLib\Objects;

    use BadMethodCallException;
    use PpmProcLib\Abstracts\AbstractPipes;
    use PpmProcLib\Process;

    /**
     * Class UnixPipes
     * @package ProcLib\Pipes
     */
    class UnixPipes extends AbstractPipes
    {
        /**
         * @var bool|null
         */
        private $ttyMode;

        /**
         * @var bool
         */
        private $ptyMode;

        /**
         * @var bool
         */
        private $haveReadSupport;

        /**
         * UnixPipes constructor.
         * @param bool|null $ttyMode
         * @param bool $ptyMode
         * @param $input
         * @param bool $haveReadSupport
         */
        public function __construct(?bool $ttyMode, bool $ptyMode, $input, bool $haveReadSupport)
        {
            $this->ttyMode = $ttyMode;
            $this->ptyMode = $ptyMode;
            $this->haveReadSupport = $haveReadSupport;

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
         * @inheritDoc
         * @return array|string[][]
         */
        public function getDescriptors(): array
        {
            if (!$this->haveReadSupport)
            {
                $nullstream = fopen('/dev/null', 'c');

                return [
                    ['pipe', 'r'],
                    $nullstream,
                    $nullstream,
                ];
            }

            if ($this->ttyMode)
            {
                return [
                    ['file', '/dev/tty', 'r'],
                    ['file', '/dev/tty', 'w'],
                    ['file', '/dev/tty', 'w'],
                ];
            }

            if ($this->ptyMode && Process::isPtySupported())
            {
                return [
                    ['pty'],
                    ['pty'],
                    ['pty'],
                ];
            }

            return [
                ['pipe', 'r'],
                ['pipe', 'w'], // stdout
                ['pipe', 'w'], // stderr
            ];
        }

        /**
         * @return array
         * @inheritDoc
         */
        public function getFiles(): array
        {
            return [];
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

            $read = $e = [];
            $r = $this->pipes;
            unset($r[0]);

            // let's have a look if something changed in streams
            set_error_handler([$this, 'handleError']);
            if (($r || $w) && false === stream_select($r, $w, $e, 0, $blocking ? Process::TIMEOUT_PRECISION * 1E6 : 0))
            {
                restore_error_handler();
                // if a system call has been interrupted, forget about it, let's try again
                // otherwise, an error occurred, let's reset pipes
                if (!$this->hasSystemCallBeenInterrupted())
                {
                    $this->pipes = [];
                }

                return $read;
            }
            restore_error_handler();

            foreach ($r as $pipe)
            {
                // prior PHP 5.4 the array passed to stream_select is modified and
                // lose key association, we have to find back the key
                $read[$type = array_search($pipe, $this->pipes, true)] = '';

                do
                {
                    $data = @fread($pipe, self::CHUNK_SIZE);
                    $read[$type] .= $data;
                } while (
                    isset($data[0]) && ($close || isset($data[self::CHUNK_SIZE - 1]))
                );

                if (!isset($read[$type][0]))
                {
                    unset($read[$type]);
                }

                if ($close && feof($pipe))
                {
                    fclose($pipe);
                    unset($this->pipes[$type]);
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
            return (bool) $this->pipes;
        }
    }
