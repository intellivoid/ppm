<?php


    namespace ppm\Objects;


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
    }