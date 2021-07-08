<?php

declare(strict_types=1);

namespace PpmParser\Builder;

use PpmParser;
use PpmParser\BuilderHelpers;
use PpmParser\Node;
use PpmParser\Node\Const_;
use PpmParser\Node\Identifier;
use PpmParser\Node\Stmt;

class ClassConst implements PpmParser\Builder
{
    protected $flags = 0;
    protected $attributes = [];
    protected $constants = [];

    /** @var Node\AttributeGroup[] */
    protected $attributeGroups = [];

    /**
     * Creates a class constant builder
     *
     * @param string|Identifier                          $name  Name
     * @param Node\Expr|bool|null|int|float|string|array $value Value
     */
    public function __construct($name, $value) {
        $this->constants = [new Const_($name, BuilderHelpers::normalizeValue($value))];
    }

    /**
     * Add another constant to const group
     *
     * @param string|Identifier                          $name  Name
     * @param Node\Expr|bool|null|int|float|string|array $value Value
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addConst($name, $value) {
        $this->constants[] = new Const_($name, BuilderHelpers::normalizeValue($value));

        return $this;
    }

    /**
     * Makes the constant public.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makePublic() {
        $this->flags = BuilderHelpers::addModifier($this->flags, Stmt\Class_::MODIFIER_PUBLIC);

        return $this;
    }

    /**
     * Makes the constant protected.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeProtected() {
        $this->flags = BuilderHelpers::addModifier($this->flags, Stmt\Class_::MODIFIER_PROTECTED);

        return $this;
    }

    /**
     * Makes the constant private.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makePrivate() {
        $this->flags = BuilderHelpers::addModifier($this->flags, Stmt\Class_::MODIFIER_PRIVATE);

        return $this;
    }

    /**
     * Sets doc comment for the constant.
     *
     * @param PpmParser\Comment\Doc|string $docComment Doc comment to set
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function setDocComment($docComment) {
        $this->attributes = [
            'comments' => [BuilderHelpers::normalizeDocComment($docComment)]
        ];

        return $this;
    }

    /**
     * Adds an attribute group.
     *
     * @param Node\Attribute|Node\AttributeGroup $attribute
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addAttribute($attribute) {
        $this->attributeGroups[] = BuilderHelpers::normalizeAttribute($attribute);

        return $this;
    }

    /**
     * Returns the built class node.
     *
     * @return Stmt\ClassConst The built constant node
     */
    public function getNode(): PpmParser\Node {
        return new Stmt\ClassConst(
            $this->constants,
            $this->flags,
            $this->attributes,
            $this->attributeGroups
        );
    }
}
