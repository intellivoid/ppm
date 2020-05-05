<?php declare(strict_types=1);

    namespace PpmParser\Builder;

    use PpmParser;
    use PpmParser\BuilderHelpers;
    use PpmParser\Node;
    use PpmParser\Node\Stmt;

    /**
     * Class Namespace_
     * @package PpmParser\Builder
     */
    class Namespace_ extends Declaration
    {
        private $name;
        private $stmts = [];

        /**
         * Creates a namespace builder.
         *
         * @param Node\Name|string|null $name Name of the namespace
         */
        public function __construct($name)
        {
            $this->name = null !== $name ? BuilderHelpers::normalizeName($name) : null;
        }

        /**
         * Adds a statement.
         *
         * @param Node|PpmParser\Builder $stmt The statement to add
         *
         * @return $this The builder instance (for fluid interface)
         */
        public function addStmt($stmt)
        {
            $this->stmts[] = BuilderHelpers::normalizeStmt($stmt);

            return $this;
        }

        /**
         * Returns the built node.
         *
         * @return Node The built node
         */
        public function getNode() : Node
        {
            return new Stmt\Namespace_($this->name, $this->stmts, $this->attributes);
        }
    }
