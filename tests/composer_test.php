<?php

    /** @noinspection PhpIncludeInspection */
    require_once("ppm");

    $lock = \ppm\Classes\Composer\Factory::parse("/mnt/c/Users/Netkas/Documents/gits/composertest/composer.lock");
    $json = \ppm\Classes\Composer\Factory::parse("/mnt/c/Users/Netkas/Documents/gits/composertest/composer.json");

    foreach($lock->getPackages() as $package)
    {
        var_dump($package["version"]);
        print($package["version"]["name"] . "==" . $package["version"]["version"] . PHP_EOL);
    }

    foreach($lock->getPackagesDev() as $package)
    {
        var_dump($package["version"]);
        print($package["version"]["name"] . "==" . $package["version"]["version"] . PHP_EOL);
    }