<?php

    // Composer
    include_once( __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Service" . DIRECTORY_SEPARATOR . "IsThisEmpty.php");
    include_once( __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Service" . DIRECTORY_SEPARATOR . "AbstractMap.php");
    include_once( __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Service" . DIRECTORY_SEPARATOR . "AbstractClass.php");
    include_once( __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Service" . DIRECTORY_SEPARATOR . "NamespaceMap.php");
    include_once( __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Service" . DIRECTORY_SEPARATOR . "PackageMap.php");
    include_once( __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Service" . DIRECTORY_SEPARATOR . "PropertyHelper.php");

    include_once( __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Json" . DIRECTORY_SEPARATOR . "Archive.php");
    include_once( __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Json" . DIRECTORY_SEPARATOR . "Author.php");
    include_once( __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Json" . DIRECTORY_SEPARATOR . "Autoload.php");
    include_once( __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Json" . DIRECTORY_SEPARATOR . "Config.php");
    include_once( __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Json" . DIRECTORY_SEPARATOR . "Scripts.php");
    include_once( __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Json" . DIRECTORY_SEPARATOR . "Support.php");
    include_once( __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "ComposerJson.php");
    include_once( __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Json" . DIRECTORY_SEPARATOR . "Repository.php");

    include_once( __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Lockfile" . DIRECTORY_SEPARATOR . "Source.php");
    include_once( __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Lockfile" . DIRECTORY_SEPARATOR . "Dist.php");
    include_once( __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Lockfile" . DIRECTORY_SEPARATOR . "Source.php");

    include_once( __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Lockfile.php");
    include_once( __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Factory.php");
    include_once( __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Classes" . DIRECTORY_SEPARATOR . "Composer" . DIRECTORY_SEPARATOR . "Wrapper.php");


    // Load the compiler
    if(class_exists("PpmParser\Parser") == false)
    {
        include_once(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "PpmParser" . DIRECTORY_SEPARATOR . "PpmParser.php");
    }