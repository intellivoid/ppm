<?php


    namespace PpmProcLib\Exceptions;


    use PpmProcLib\Process;

    /**
     * Class ProcessSignaledException
     * @package ProcLib\Exceptions
     */
    final class ProcessSignaledException extends RuntimeException
    {
        /**
         * @var Process
         */
        private $process;

        /**
         * ProcessSignaledException constructor.
         * @param Process $process
         */
        public function __construct(Process $process)
        {
            $this->process = $process;

            parent::__construct(sprintf('The process has been signaled with signal "%s".', $process->getTermSignal()));
        }

        /**
         * @return Process
         */
        public function getProcess(): Process
        {
            return $this->process;
        }

        /**
         * @return int
         */
        public function getSignal(): int
        {
            return $this->getProcess()->getTermSignal();
        }
    }
