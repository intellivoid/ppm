<?php

    require("ppm");
    require_once(
        __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR .
        "src" . DIRECTORY_SEPARATOR . "ppm" . DIRECTORY_SEPARATOR . "PpmPython" . DIRECTORY_SEPARATOR . "PpmPython.php"
    );

    \ppm\Utilities\CLI::$Stdout = true;
    \ppm\Utilities\CLI::$VerboseMode = true;

    var_dump(\PpmPython\Classes\Python::detectInstalledVersions());