<?php

// Load the Laravel application
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Handle the request and send the response
$app->handleRequest(
    Illuminate\Http\Request::capture()
);

