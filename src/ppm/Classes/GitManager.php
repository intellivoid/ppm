<?php /** @noinspection PhpUnused */


    namespace ppm\Classes;

    use Exception;
    use ppm\Objects\GitRepo;

    /**
     * Class GitManager
     * @package ppm\Classes
     */
    class GitManager
    {
        /**
         * Git executable location
         *
         * @var string
         */
        protected static $bin = '/usr/bin/git';

        /**
         * Sets git executable path
         *
         * @param string $path executable location
         */
        public static function set_bin($path)
        {
            self::$bin = $path;
        }

        /**
         * Gets git executable path
         */
        public static function get_bin()
        {
            return self::$bin;
        }

        /**
         * Sets up library for use in a default Windows environment
         */
        public static function windows_mode()
        {
            self::set_bin('git');
        }

        /**
         * Create a new git repository
         *
         * Accepts a creation path, and, optionally, a source path
         *
         * @access  public
         * @param string  repository path
         * @param string  directory to source
         * @return  GitRepo
         * @throws Exception
         * @throws Exception
         */
        public static function &create($repo_path, $source = null)
        {
            return GitRepo::create_new($repo_path, $source);
        }

        /**
         * Open an existing git repository
         *
         * Accepts a repository path
         *
         * @access  public
         * @param string  repository path
         * @return  GitRepo
         * @throws Exception
         * @throws Exception
         */
        public static function open($repo_path)
        {
            return new GitRepo($repo_path);
        }

        /**
         * Clones a remote repo into a directory and then returns a GitRepo object
         * for the newly created local repo
         *
         * Accepts a creation path and a remote to clone from
         *
         * @access  public
         * @param string  repository path
         * @param string  remote source
         * @param string  reference path
         * @return  GitRepo
         *
         * @throws Exception
         */
        public static function &clone_remote($repo_path, $remote, $reference = null)
        {
            //Changed the below boolean from true to false, since this appears to be a bug when not using a reference repo.  A more robust solution may be appropriate to make it work with AND without a reference.
            return GitRepo::create_new($repo_path, $remote, false, $reference);
        }

        /**
         * Checks if a variable is an instance of GitRepo
         *
         * Accepts a variable
         *
         * @access  public
         * @param   mixed   variable
         * @return  bool
         */
        public static function is_repo($var)
        {
            return (get_class($var) == 'GitRepo');
        }

    }