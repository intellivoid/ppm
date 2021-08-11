<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace PpmProcLib\Exceptions;

    use PpmProcLib\Process;

    /**
     * Class ProcessTimedOutException
     * @package ProcLib\Exceptions
     */
    class ProcessTimedOutException extends RuntimeException
    {
        public const TYPE_GENERAL = 1;
        public const TYPE_IDLE = 2;

        /**
         * @var Process
         */
        private $process;

        /**
         * @var int
         */
        private $timeoutType;

        /**
         * ProcessTimedOutException constructor.
         * @param Process $process
         * @param int $timeoutType
         */
        public function __construct(Process $process, int $timeoutType)
        {
            $this->process = $process;
            $this->timeoutType = $timeoutType;

            parent::__construct(sprintf(
                'The process "%s" exceeded the timeout of %s seconds.',
                $process->getCommandLine(),
                $this->getExceededTimeout()
            ));
        }

        /**
         * @return Process
         */
        public function getProcess(): Process
        {
            return $this->process;
        }

        /**
         * @return bool
         */
        public function isGeneralTimeout(): bool
        {
            return self::TYPE_GENERAL === $this->timeoutType;
        }

        /**
         * @return bool
         */
        public function isIdleTimeout(): bool
        {
            return self::TYPE_IDLE === $this->timeoutType;
        }

        /**
         * @return float|null
         * @noinspection PhpSwitchCanBeReplacedWithMatchExpressionInspection
         */
        public function getExceededTimeout(): ?float
        {
            switch ($this->timeoutType)
            {
                case self::TYPE_GENERAL:
                    return $this->process->getTimeout();

                case self::TYPE_IDLE:
                    return $this->process->getIdleTimeout();

                default:
                    throw new \LogicException(sprintf('Unknown timeout type "%d".', $this->timeoutType));
            }
        }
    }
