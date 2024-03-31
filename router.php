<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use encritary\registerAuth\App;
use encritary\registerAuth\request\Request;

$app = new App();
$response = $app->handleRequest(Request::fromGlobals());
$response->echo();