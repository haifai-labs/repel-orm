<?php
require dirname(__DIR__) . '/autoloader.php';

use Repel\Includes\CLI;
use Repel\Adapter\Adapter;
use Repel\Adapter\Generator;
use Repel\Adapter\Fetcher;
use Repel\Framework;

try {
    if ($argv[1] !== null) {
        $config_file = $argv[1];
        if (file_exists($config_file)) {
            $config = require $config_file;

            if (!key_exists('databases', $config) || !Framework\RDatabaseConfigChecker::checkConfig($config["databases"])) {
                echo "Incorrect database config file.";
                die;
            }
        } else {
            echo "Database config file not found.";
            die;
        }
    } else {
        echo "No database config file.";
        die;
    }

    foreach ($config['databases'] as $key => $c) {
        if (!key_exists('model_directory_path', $c)) {
            echo CLI::failure("No 'model_directory_path' definition in database {$key}");
            return false;
        }

        $adapter = new Adapter($config['databases'], $c["adapter"]);

        if (key_exists('relationship_file', $c)) {
            $adapter->addFetcher(new Fetcher\PhpManyToManyFetcher($c["relationship_file"], $key));
        }

        switch ($c['adapter']) {
            case 'pgsql':
                $adapter->addFetcher(new Fetcher\PostgreSQLFetcher($key));
                break;
            default:
                break;
        }

        $adapter->fetch()
                ->addGenerator(new Generator\RepelGenerator($c, $key))
                ->generate();

        echo CLI::success();
    }
} catch (Exception $ex) {
    echo CLI::failure($ex);
    die();
}
