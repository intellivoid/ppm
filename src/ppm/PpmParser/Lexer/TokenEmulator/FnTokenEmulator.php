<?php declare(strict_types=1);

namespace PpmParser\Lexer\TokenEmulator;

use PpmParser\Lexer\Emulative;

final class FnTokenEmulator extends KeywordEmulator
{
    public function getPhpVersion(): string
    {
        return Emulative::PHP_7_4;
    }

    public function getKeywordString(): string
    {
        return 'fn';
    }

    public function getKeywordToken(): int
    {
        return \T_FN;
    }
}