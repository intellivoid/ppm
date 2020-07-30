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
         * @var PersonalAccessToken[]
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
            $selected_access_token = null;

            foreach($this->AccessTokens as $personalAccessToken)
            {
                if($personalAccessToken->Alias == $alias)
                {
                    $selected_access_token = $personalAccessToken;
                }
            }

            if($selected_access_token == null)
            {
                throw new GithubPersonalAccessTokenNotFoundException();
            }

            return $selected_access_token;
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
            try
            {
                $this->get($alias);
                throw new GithubPersonalAccessTokenAlreadyExistsException();
            }
            catch (GithubPersonalAccessTokenNotFoundException $e)
            {
                unset($e);
            }

            $PersonalAccessToken = new PersonalAccessToken();
            $PersonalAccessToken->Alias = $alias;
            $PersonalAccessToken->PersonalAccessToken = $personal_access_token;
            $PersonalAccessToken->AddedTimestamp = (int)time();
            $PersonalAccessToken->LastUsedTimestamp = 0;

            $this->AccessTokens[] = $PersonalAccessToken;

            return $PersonalAccessToken;
        }
    }