#!/usr/bin/env php
<?php
require dirname(__DIR__) . '/autoloader.php';

use Repel\Includes\CLI;
use Repel\Framework\DatabaseManager;
use Repel\Framework;

const DOT_FILL = 30;
        const HEADER_FILL = 32;

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

    $drivers = array();

    foreach ($config['databases'] as $key => $c) {
        echo CLI::h1('create default database', HEADER_FILL);
        // Connecting
        echo CLI::dotFill('connecting', DOT_FILL);
        $manager = new DatabaseManager($c);
        echo CLI::color("done", green);
        echo "\n";
        $result  = $manager->db->exec('BEGIN;');
        if ($result === false) {
            $errorInfo = $this->db->errorInfo();
            throw new Exception('SQL ERROR: ' . "\n" . $errorInfo [2]);
        }

        if (!in_array($c["driver"], $drivers)) {
            // Creating schema
            $count = 0;
            echo CLI::dotFill('creating schema', DOT_FILL);
            $manager->createSchema();
            echo CLI::color("done", green);
            echo "\n";
        }

        // Initializing structure
        echo CLI::dotFill('initializing', DOT_FILL);
        $manager->initialize();
        echo CLI::color("done", green);
        echo "\n";

        if (!in_array($c["driver"], $drivers)) {
            // Removing old schemas
            echo CLI::dotFill('removing backups', DOT_FILL);
            $manager->removeBackups();
            echo CLI::color("done", green);
            echo "\n";

            $drivers[] = $c["driver"];
        }

        $result = $manager->db->exec('COMMIT;');
        if ($result === false) {
            $errorInfo = $this->db->errorInfo();
            throw new Exception('SQL ERROR: ' . "\n" . $errorInfo [2]);
        }
        echo CLI::color("SUCCESS", 'white', 'green');
        echo "\n";
    }
} catch (Exception $e) {
    if (isset($manager->db)) {
        $result = $manager->db->exec('ROLLBACK;');
    }
    echo CLI::color("failed", red);
    echo "\n";
    echo "\n";
    echo CLI::color($e->getMessage(), 'white', 'red');
    echo "\n";
    echo "\n";
    die();
}
?>
