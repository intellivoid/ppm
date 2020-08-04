<?php
    namespace PpmZiProto\TypeTransformer;

    use PpmZiProto\Packet;
    use PpmZiProto\Type\Map;

    /**
     * Class MapTransformer
     * @package ZiProto\TypeTransformer
     */
    abstract class MapTransformer
    {
        /**
         * @param Packet $packer
         * @param $value
         * @return string
         */
        public function encode(Packet $packer, $value): string
        {
            if ($value instanceof Map)
            {
                return $packer->encodeMap($value->map);
            }
            else
            {
                return null;
            }
        }
    }