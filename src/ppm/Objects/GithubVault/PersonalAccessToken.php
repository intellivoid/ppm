<?php


    namespace ppm\Objects\GithubVault;

    /**
     * Class PersonalAccessToken
     * @package ppm\Objects\GithubVault
     */
    class PersonalAccessToken
    {
        /**
         * The alias for this personal access token
         *
         * @var string
         */
        public $Alias;

        /**
         * The personal access token
         *
         * @var string
         */
        public $PersonalAccessToken;

        /**
         * The Unix Timestamp of when this personal access token was last used
         *
         * @var int
         */
        public $LastUsedTimestamp;

        /**
         * The Unix Timestamp of when this personal access token was added
         *
         * @var int
         */
        public $AddedTimestamp;

        /**
         * Returns an array which represents this object structure
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                "alias" => $this->Alias,
                "personal_access_token" => $this->PersonalAccessToken,
                "last_used_timestamp" => (int)$this->LastUsedTimestamp,
                "added_timestamp" => (int)$this->AddedTimestamp
            );
        }

        /**
         * Constructs object from array
         *
         * @param array $data
         * @return PersonalAccessToken
         */
        public static function fromArray(array $data): PersonalAccessToken
        {
            $PersonalAccessTokenObject = new PersonalAccessToken();

            if(isset($data["alias"]))
            {
                $PersonalAccessTokenObject->Alias = $data["alias"];
            }

            if(isset($data["personal_access_token"]))
            {
                $PersonalAccessTokenObject->PersonalAccessToken = $data["personal_access_token"];
            }

            if(isset($data["last_used_timestamp"]))
            {
                $PersonalAccessTokenObject->LastUsedTimestamp = (int)$data["last_used_timestamp"];
            }

            if(isset($data["added_timestamp"]))
            {
                $PersonalAccessTokenObject->AddedTimestamp = (int)$data["added_timestamp"];
            }

            return $PersonalAccessTokenObject;
        }
    }