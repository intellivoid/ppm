<?php
    namespace PpmZiProto\TypeTransformer;

    use PpmZiProto\Packet;

    /**
     * Interface Validator
     * @package ZiProto\TypeTransformer
     */
    interface Validator
    {
        /**
         * @param Packet $packer
         * @param $value
         * @return string
         */
        public function check(Packet $packer, $value) :string;
    }