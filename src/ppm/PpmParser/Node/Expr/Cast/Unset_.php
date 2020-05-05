<?php declare(strict_types=1);

namespace PpmParser\Node\Expr\Cast;

use PpmParser\Node\Expr\Cast;

class Unset_ extends Cast
{
    public function getType() : string {
        return 'Expr_Cast_Unset';
    }
}
