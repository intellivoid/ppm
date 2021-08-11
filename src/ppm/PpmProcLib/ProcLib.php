<?php

    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'Interfaces' . DIRECTORY_SEPARATOR . 'PipesInterface.php');
    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'AbstractPipes.php');
    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'Types' . DIRECTORY_SEPARATOR . 'IterationType.php');
    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'Types' . DIRECTORY_SEPARATOR . 'StatusType.php');
    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'Types' . DIRECTORY_SEPARATOR . 'StdType.php');
    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'Types' . DIRECTORY_SEPARATOR . 'StreamType.php');
    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'Interfaces' . DIRECTORY_SEPARATOR . 'ExceptionInterface.php');
    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidArgumentException.php');
    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'LogicException.php');
    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'RuntimeException.php');
    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'ProcessFailedException.php');
    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'ProcessSignaledException.php');
    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'ProcessTimedOutException.php');
    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'UnixPipes.php');
    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'WindowsPipes.php');
    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'Process.php');
    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'PhpProcess.php');
    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'Utilities' . DIRECTORY_SEPARATOR . 'ExecutableFinder.php');
    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'Utilities' . DIRECTORY_SEPARATOR . 'InputStream.php');
    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'Utilities' . DIRECTORY_SEPARATOR . 'PhpExecutableFinder.php');
    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'Utilities' . DIRECTORY_SEPARATOR . 'ProcessUtils.php');
