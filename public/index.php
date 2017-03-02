<?php

require __DIR__ . "/../bootstrap/autoload.php";

require __DIR__ . '/../bootstrap/app.php';

// Mounted images controller
$app->mount('/', new \IU\Controllers\ImagesController);

$app->run();