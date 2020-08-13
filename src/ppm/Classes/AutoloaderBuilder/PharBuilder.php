<?php


    namespace ppm\Classes\AutoloaderBuilder;


    use InvalidArgumentException;
    use Phar;
    use ppm\Classes\DirectoryScanner\DirectoryScanner;

    /**
     * Class PharBuilder
     * @package ppm\Classes\AutoloaderBuilder
     */
    class PharBuilder
    {

        private $scanner;
        private $compression;
        private $key;
        private $basedir;
        private $aliasName;
        private $signatureType;

        private $directories = array();

        private $supportedSignatureTypes = array(
            'SHA-512' => Phar::SHA512,
            'SHA-256' => Phar::SHA256,
            'SHA-1' => Phar::SHA1
        );

        public function __construct(DirectoryScanner $scanner, $basedir)
        {
            $this->scanner = $scanner;
            $this->basedir = $basedir;
        }

        public function setCompressionMode($mode)
        {
            $this->compression = $mode;
        }

        public function setSignatureType($type)
        {
            if (!in_array($type, array_keys($this->supportedSignatureTypes)))
            {
                throw new InvalidArgumentException(
                    sprintf('Signature type "%s" not known or not supported by this PHP installation.', $type)
                );
            }
            $this->signatureType = $type;
        }

        public function setSignatureKey($key)
        {
            $this->key = $key;
        }

        public function addDirectory($directory)
        {
            $this->directories[] = $directory;
        }

        public function setAliasName($name)
        {
            $this->aliasName = $name;
        }

        public function build($filename, $stub)
        {
            if (file_exists($filename))
            {
                unlink($filename);
            }
            $phar = new Phar($filename, 0, $this->aliasName != '' ? $this->aliasName : basename($filename));
            $phar->startBuffering();
            $phar->setStub($stub);
            if ($this->key !== NULL)
            {
                $privateKey = '';
                openssl_pkey_export($this->key, $privateKey);
                $phar->setSignatureAlgorithm(Phar::OPENSSL, $privateKey);
                $keyDetails = openssl_pkey_get_details($this->key);
                file_put_contents($filename . '.pubkey', $keyDetails['key']);
            }
            else
            {
                $phar->setSignatureAlgorithm($this->selectSignatureType());
            }

            $basedir = $this->basedir ? $this->basedir : $this->directories[0];
            foreach($this->directories as $directory)
            {
                $phar->buildFromIterator($this->scanner->__invoke($directory), $basedir);
            }

            if ($this->compression !== Phar::NONE)
            {
                $phar->compressFiles($this->compression);
            }
            $phar->stopBuffering();
        }

        private function selectSignatureType()
        {
            if ($this->signatureType !== NULL)
            {
                return $this->supportedSignatureTypes[$this->signatureType];
            }
            $supported = Phar::getSupportedSignatures();
            foreach($this->supportedSignatureTypes as $candidate => $type)
            {
                if (in_array($candidate, $supported))
                {
                    return $type;
                }
            }

            // Is there any PHP Version out there that does not support at least SHA-1?
            // But hey, fallback to md5, better than nothing
            return Phar::MD5;
        }
    }