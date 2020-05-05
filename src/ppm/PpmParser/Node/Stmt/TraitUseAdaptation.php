<?php declare(strict_types=1);

namespace PpmParser\Node\Stmt;

use PpmParser\Node;

abstract class TraitUseAdaptation extends Node\Stmt
{
    /** @var Node\Name|null Trait name */
    public $trait;
    /** @var Node\Identifier Method name */
    public $method;
}
