<?php /** @noinspection PhpUnusedPrivateFieldInspection */


    namespace ppm\Classes\AutoloaderBuilder;

    /**
     * Class StaticRenderer
     * @package ppm\Classes\AutoloaderBuilder
     */
    class StaticRenderer extends AutoloadRenderer
    {

        private $dependencies;
        private $phar;
        private $require = 'require';

        /**
         * @var StaticListRenderer
         */
        private $renderHelper;

        /**
         * StaticRenderer constructor.
         * @param array $classlist
         * @param StaticListRenderer $renderHelper
         */
        public function __construct(array $classlist, StaticListRenderer $renderHelper)
        {
            parent::__construct($classlist);
            $this->renderHelper = $renderHelper;
        }

        /**
         * Setter for Dependency Array
         * @param array $dep Dependency Array from classfinder
         */
        public function setDependencies(Array $dep)
        {
            $this->dependencies = $dep;
        }

        /**
         * Toggle phar outut mode
         *
         * @param boolean $phar
         */
        public function setPharMode($phar)
        {
            $this->phar = (boolean)$phar;
        }

        /**
         * Toggle wether or not to use require_once over require
         *
         * @param boolean $mode
         */
        public function setRequireOnce($mode)
        {
        }


        /**
         * @param string $template
         *
         * @return string
         */
        public function render($template)
        {
            $baseDir = '';
            if ($this->phar)
            {
                $baseDir = "'phar://". $this->variables['___PHAR___']."' . ";
            }
            elseif ($this->baseDir)
            {
                $baseDir = $this->compat ? 'dirname(__FILE__) . ' : '__DIR__ . ';
            }

            $entries = array();
            foreach($this->sortByDependency() as $fname)
            {
                $entries[] = $this->resolvePath($fname);
            }

            $replace = array_merge(
                $this->variables,
                array(
                    '___CREATED___'   => date( $this->dateformat, $this->timestamp ? $this->timestamp : time()),
                    '___FILELIST___' => $this->renderHelper->render($entries),
                    '___BASEDIR___'   => $baseDir,
                    '___AUTOLOAD___'  => uniqid('autoload', true)
                )
            );

            return str_replace(array_keys($replace), array_values($replace), $template);
        }

        /**
         * Helper to sort classes/interfaces and traits based on their depdendency info
         *
         * @return array
         */
        protected function sortByDependency()
        {
            $sorter  = new ClassDependencySorter($this->classes, $this->dependencies);
            $list    = $sorter->process();

            return array_unique($list);
        }

    }