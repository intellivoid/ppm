<?php


    namespace ppm\Objects;


    use ppm\Exceptions\GithubPersonalAccessTokenAlreadyExistsException;
    use ppm\Exceptions\GithubPersonalAccessTokenNotFoundException;
    use ppm\Objects\GithubVault\PersonalAccessToken;

    /**
     * Class GithubVault
     * @package ppm\Objects
     */
    class GithubVault
    {
        /**
         * @var array
         */
        public $AccessTokens;

        /**
         * Gets an existing personal access token from the vault
         *
         * @param string $alias
         * @return PersonalAccessToken
         * @throws GithubPersonalAccessTokenNotFoundException
         */
        public function get(string $alias): PersonalAccessToken
        {
            if(isset($this->AccessTokens[$alias]) == false)
            {
                throw new GithubPersonalAccessTokenNotFoundException();
            }

            return $this->AccessTokens[$alias];
        }

        /**
         * Adds a new personal access token to the vault
         *
         * @param string $alias
         * @param string $personal_access_token
         * @return PersonalAccessToken
         * @throws GithubPersonalAccessTokenAlreadyExistsException
         */
        public function add(string $alias, string $personal_access_token): PersonalAccessToken
        {
            if(isset($this->AccessTokens[$alias]))
            {
                throw new GithubPersonalAccessTokenAlreadyExistsException();
            }

            $PersonalAccessToken = new PersonalAccessToken();
            $PersonalAccessToken->Alias = $alias;
            $PersonalAccessToken->PersonalAccessToken = $personal_access_token;
            $PersonalAccessToken->AddedTimestamp = (int)time();
            $PersonalAccessToken->LastUsedTimestamp = 0;

            $this->AccessTokens[$alias] = $PersonalAccessToken;

            return $PersonalAccessToken;
        }

        /**
         * Updates an existing personal access token in the vault
         *
         * @param PersonalAccessToken $personalAccessToken
         * @return bool
         */
        public function update(PersonalAccessToken $personalAccessToken): bool
        {
            $this->AccessTokens[$personalAccessToken->Alias] = $personalAccessToken;
            return true;
        }
    }