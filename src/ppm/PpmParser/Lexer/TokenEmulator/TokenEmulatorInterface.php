<?php declare(strict_types=1);

namespace PpmParser\Lexer\TokenEmulator;

/** @internal */
interface TokenEmulatorInterface
{
    public function isEmulationNeeded(string $code): bool;

    /**
     * @return array Modified Tokens
     */
    public function emulate(string $code, array $tokens): array;
}
