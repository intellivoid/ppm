<?php


    namespace ppm\Objects\Sources;


    use Exception;

    /**
     * Class ComposerSource
     * @package ppm\Objects\Sources
     */
    class ComposerSource
    {
        /**
         * The vendor that created the package
         *
         * @var string
         */
        public $VendorName;

        /**
         * The name of the package to install
         *
         * @var string
         */
        public $PackageName;

        /**
         * @param string $input
         * @return ComposerSource
         * @throws Exception
         */
        public static function parse(string $input): ComposerSource
        {
            $parsed_syntax = explode("@", $input);
            if(count($parsed_syntax) < 2)
            {
                throw new Exception("Cannot parse Composer source syntax, vendor@composer/package");
            }

            $parsed_syntax2 = explode("/", $parsed_syntax[1]);
            if(count($parsed_syntax2) < 2)
            {
                throw new Exception("Cannot parse Composer source syntax, vendor@composer/package");
            }

            $ComposerSource = new ComposerSource();
            $ComposerSource->VendorName = $parsed_syntax[0];
            $ComposerSource->PackageName = $parsed_syntax2[1];

            return $ComposerSource;
        }

        public function getPackageName(): string
        {
            return $this->VendorName . "/" . $this->PackageName;
        }

        public function comparePackageName(string $input): bool
        {
            if(strtolower($this->getPackageName()) == strtolower($input))
            {
                return true;
            }

            return false;
        }

        /**
         * Returns a hash of this source
         *
         * @return string
         */
        public function toHash(): string
        {
            return hash("sha1", $this->VendorName . "/" . $this->PackageName);
        }

        /**
         * @return string
         */
        public function __toString()
        {
            return $this->VendorName . "@composer/" . $this->PackageName;
        }
    }