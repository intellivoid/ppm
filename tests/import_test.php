<?php

    require(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'ppmstrap.php');

    ppm_import("com.intellivoid.mongodb_driver");

    $client = new MongoDB\Client(
        "mongodb://127.0.0.127017",
        array(
            "username" => "admin",
            "password" => "admin"
        )
    );

    $client->selectCollection("test", "None");
    var_dump($client);