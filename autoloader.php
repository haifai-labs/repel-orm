<?php

if (file_exists($file = __DIR__ . '/vendor/autoload.php')) {
    $loader = require $file;
    $loader->add('Repel', array(__DIR__.'/src/'));
    $loader->add('data', array(__DIR__.'/'));
    $loader->register();
}
