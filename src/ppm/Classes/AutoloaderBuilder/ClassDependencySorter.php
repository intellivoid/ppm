<?php


    namespace ppm\Classes\AutoloaderBuilder;


    use ppm\Exceptions\ClassDependencySorterException;

    /**
     * Sorting classes by dependency for static requires
     */
    class ClassDependencySorter
    {

        private $classList;
        private $dependencies;

        private $level;

        private $sorted = array();

        public function __construct(Array $classes, Array $dependencies) {
            $this->classList    = $classes;
            $this->dependencies = $dependencies;
        }

        public function process() {
            $this->level = 0;
            foreach($this->classList as $class => $file) {
                if (!in_array($class, $this->sorted)) {
                    $this->resolve($class);
                }
            }

            $res = array();
            foreach($this->sorted as $class) {
                if (!isset($this->classList[$class])) {
                    continue;
                }
                $res[$class] = $this->classList[$class];
            }
            return $res;
        }

        private function resolve($class) {
            $this->level++;
            if ($this->level == 50) {
                throw new ClassDependencySorterException("Can't resolve more than 50 levels of dependencies", ClassDependencySorterException::TooManyDependencyLevels);
            }
            if (isset($this->dependencies[$class])) {
                foreach($this->dependencies[$class] as $depclass) {
                    if (!in_array($depclass, $this->sorted)) {
                        $this->resolve($depclass);
                    }
                }
            }
            $this->sorted[] = $class;
            $this->level--;
        }
    }