<?php


    namespace ppm\Utilities\CLI;


    use ppm\Exceptions\GithubPersonalAccessTokenAlreadyExistsException;
    use ppm\Exceptions\GithubPersonalAccessTokenNotFoundException;
    use ppm\Utilities\CLI;
    use ppm\Utilities\IO;
    use ppm\Utilities\PathFinder;
    use ppm\Utilities\System;

    class GithubVault
    {
        /**
         * Removes a personal access key from the Github vault
         */
        public static function githubRemovePersonalAccessKey()
        {
            if(System::isRoot() == false)
            {
                CLI::logError("This operation requires root privileges, please run ppm with 'sudo -H'");
                exit(1);
            }

            if(IO::writeTest(PathFinder::getMainPath(true)) == false)
            {
                CLI::logError("Write test failed, cannot write to the PPM installation directory");
                exit(1);
            }

            $github_vault = new \ppm\Objects\GithubVault();
            $github_vault->load();

            try
            {
                $personal_access_token = $github_vault->get(CLI::getParameter("alias", "Alias", false));
                $github_vault->delete($personal_access_token);
            }
            catch (GithubPersonalAccessTokenNotFoundException $e)
            {
                CLI::logError("Alias not registered in vault, aborting.");
                exit(1);
            }

            $github_vault->save();
            print("Personal Access Token removed." . PHP_EOL);
        }

        /**
         * Adds a personal access token to the Github vault
         */
        public static function githubAddPersonalAccessKey()
        {
            if(System::isRoot() == false)
            {
                CLI::logError("This operation requires root privileges, please run ppm with 'sudo -H'");
                exit(1);
            }

            if(IO::writeTest(PathFinder::getMainPath(true)) == false)
            {
                CLI::logError("Write test failed, cannot write to the PPM installation directory");
                exit(1);
            }

            $github_vault = new \ppm\Objects\GithubVault();
            $github_vault->load();

            $alias = CLI::getParameter("alias", "Alias", false);
            $personal_access_token = CLI::getParameter("token", "Personal Access Token", false);

            try
            {
                $github_vault->add($alias, $personal_access_token);
            }
            catch (GithubPersonalAccessTokenAlreadyExistsException $e)
            {
                CLI::logError("Personal Access Token already defined in the Github vault, aborting.");
                exit(1);
            }

            $github_vault->save();
            print("Personal Access Token added." . PHP_EOL);
        }
    }