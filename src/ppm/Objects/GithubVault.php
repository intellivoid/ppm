<?php


    namespace ppm\Objects;


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
        }
    }