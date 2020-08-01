<?php


    namespace ppm\Objects\Sources;

    use Exception;
    use ppm\Objects\GithubVault\PersonalAccessToken;

    /**
     * Class Github
     * @package ppm\Objects\Sources
     */
    class GithubSource
    {
        /**
         * The selected alias
         *
         * @var string
         */
        public $Alias;

        /**
         * The selected organization
         *
         * @var string
         */
        public $Organization;

        /**
         * The selected Repository
         *
         * @var string
         */
        public $Repository;

        /**
         * Returns a hash of this source
         *
         * @return string
         */
        public function toHash(): string
        {
            return hash("sha1", $this->Alias . $this->Organization . $this->Repository);
        }

        /**
         * Returns a URI to clone the repo using the personal access token
         *
         * @param PersonalAccessToken $personalAccessToken
         * @return string
         * @noinspection PhpUnused
         */
        public function toUri(PersonalAccessToken $personalAccessToken): string
        {
            $repository = "github.com/" . $this->Organization . "/" . $this->Repository . ".git";
            $authentication = $personalAccessToken->PersonalAccessToken;

            return "https://" . $authentication . "@" . $repository;
        }

        /**
         * Returns a name that represents the repository
         *
         * @return string
         * @noinspection PhpUnused
         */
        public function toName(): string
        {
            return $this->Organization . "/" . $this->Repository . ".git";
        }

        /**
         * Parses the string into a GitHub source and returns an exception on invalid syntax
         *
         * @param string $input
         * @return GithubSource
         * @throws Exception
         */
        public static function parse(string $input): GithubSource
        {
            $parsed_syntax = explode("/", $input);
            if(count($parsed_syntax) < 3)
            {
                if(count($parsed_syntax) == 1)
                {
                    throw new Exception("Cannot parse Github source syntax, missing alias; github@alias/organization/repository");
                }

                if(count($parsed_syntax) == 2)
                {
                    throw new Exception("Cannot parse Github source syntax, missing organization; github@alias/organization/repository");
                }
            }

            $GithubSource = new GithubSource();
            $GithubSource->Alias = str_ireplace("github@", "", $parsed_syntax[0]);
            $GithubSource->Organization = $parsed_syntax[1];
            $GithubSource->Repository = $parsed_syntax[2];

            return $GithubSource;
        }
    }