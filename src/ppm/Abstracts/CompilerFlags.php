<?php


    namespace ppm\Abstracts;

    /**
     * Class CompilerFlags
     * @package ppm\Abstracts
     */
    abstract class CompilerFlags
    {
        const LintingError = 10;

        const LintingWarning = 11;

        const ByteCompilerError = 12;

        const ByteCompilerWarning = 13;
    }