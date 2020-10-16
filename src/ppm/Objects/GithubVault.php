<?php /** @noinspection PhpUnused */


    namespace ppm\Objects;


    use ppm\Exceptions\GithubPersonalAccessTokenAlreadyExistsException;
    use ppm\Exceptions\GithubPersonalAccessTokenNotFoundException;
    use ppm\Objects\GithubVault\PersonalAccessToken;
    use ppm\Utilities\PathFinder;
    use PpmZiProto\ZiProto;

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
         * The default profile used for Github Vault (default@)
         *
         * @var string
         */
        public $DefaultProfile;

        /**
         * @return PersonalAccessToken
         * @throws GithubPersonalAccessTokenNotFoundException
         */
        public function getDefaultProfile(): PersonalAccessToken
        {
            if($this->DefaultProfile !== null)
            {
                $this->get($this->DefaultProfile);
            }

            /** @var PersonalAccessToken $accessToken */
            foreach($this->AccessTokens as $accessToken)
            {
                if($this->DefaultProfile == null)
                {
                    // Set the first profile as the default profile
                    $this->DefaultProfile = $accessToken->Alias;
                    $data["default_profile"] = $this->DefaultProfile;
                    break;
                }
            }

            return $this->get($this->DefaultProfile);
        }

        /**
         * Gets an existing personal access token from the vault
         *
         * @param string $alias
         * @return PersonalAccessToken
         * @throws GithubPersonalAccessTokenNotFoundException
         */
        public function get(string $alias): PersonalAccessToken
        {
            if(strtolower($alias) == "default")
            {
                return $this->getDefaultProfile();
            }

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
         * @noinspection PhpUnused
         */
        public function update(PersonalAccessToken $personalAccessToken): bool
        {
            $this->AccessTokens[$personalAccessToken->Alias] = $personalAccessToken;
            return true;
        }

        /**
         * Removes an existing personal access token from the vault
         *
         * @param PersonalAccessToken $personalAccessToken
         * @return bool
         * @noinspection PhpUnused
         */
        public function delete(PersonalAccessToken $personalAccessToken): bool
        {
            unset($this->AccessTokens[$personalAccessToken->Alias]);
            return true;
        }

        /**
         * Saves the Github Vault to disk
         *
         * @return bool
         * @noinspection PhpUnused
         */
        public function save(): bool
        {
            $path = PathFinder::getGithubVaultPath(true);

            $data = array(
                "default_profile" => $this->DefaultProfile,
                "profiles" => [],
            );

            /** @var PersonalAccessToken $accessToken */
            foreach($this->AccessTokens as $accessToken)
            {
                if($this->DefaultProfile == null)
                {
                    // Set the first profile as the default profile
                    $this->DefaultProfile = $accessToken->Alias;
                    $data["default_profile"] = $this->DefaultProfile;
                }

                $data["profiles"][$accessToken->Alias] = $accessToken->toArray();
            }

            file_put_contents($path, ZiProto::encode($data));
            return true;
        }

        /**
         * Loads the Github Vault from disk if the file exists
         *
         * @return bool
         * @noinspection PhpUnused
         */
        public function load(): bool
        {
            $path = PathFinder::getGithubVaultPath(true);

            if(file_exists($path) == false)
            {
                return false;
            }

            $loaded_data = ZiProto::decode(file_get_contents($path));

            // Backwards compatibility
            if(isset($loaded_data["profiles"]))
            {
                $this->AccessTokens = array();

                if(isset($loaded_data["default_profile"]))
                {
                    $this->DefaultProfile = $loaded_data["default_profile"];
                }

                foreach($loaded_data["profiles"] as $datum)
                {
                    $personal_access_token = PersonalAccessToken::fromArray($datum);
                    $this->AccessTokens[$personal_access_token->Alias] = $personal_access_token;

                    // If the default profile wasn't loaded, set it as the first profile.
                    if($this->DefaultProfile == null)
                    {
                        $this->DefaultProfile = $personal_access_token->Alias;
                    }
                }
            }
            else
            {
                $this->AccessTokens = array();
                $this->DefaultProfile = null;

                foreach($loaded_data as $datum)
                {
                    $personal_access_token = PersonalAccessToken::fromArray($datum);
                    $this->AccessTokens[$personal_access_token->Alias] = $personal_access_token;

                    if($this->DefaultProfile == null)
                    {
                        // Set the first profile as the default profile
                        $this->DefaultProfile = $personal_access_token->Alias;
                        $data["default_profile"] = $this->DefaultProfile;
                    }
                }
            }

            return true;
        }
    }