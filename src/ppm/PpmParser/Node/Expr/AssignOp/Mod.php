<?php declare(strict_types=1);

namespace PpmParser\Node\Expr\AssignOp;

use PpmParser\Node\Expr\AssignOp;

class Mod extends AssignOp
{
    public function getType() : string {
        return 'Expr_AssignOp_Mod';
    }
}
