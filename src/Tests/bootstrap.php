<?php

use Symfony\Component\Dotenv\Dotenv;

// Adjust the path to include the correct location of the vendor directory
require dirname(__DIR__, 2).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__, 2).'/config/bootstrap.php')) {
    require dirname(__DIR__, 2).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__, 2).'/.env');
}
