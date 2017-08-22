<?php

$env = $app->detectEnvironment(function() {
    $env = getenv('ENV');
    $testEnv = __DIR__.'/../.env.testing';
    if ($env == 'testing') {
        if (file_exists($testEnv))
        {
            $dotenv = new Dotenv\Dotenv(__DIR__ . '/../', '.env.testing');
            $dotenv->overload();
        }
        else {
            echo 'File .env.testing was not found';
            exit;
        }
    }
});
