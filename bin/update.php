#!/usr/bin/env php
<?php
require dirname(__DIR__) . '/autoloader.php';

use Repel\Includes\CLI;
use Repel\Initiator;
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

        $old_schema = null;

        if (!in_array($c["driver"], $drivers)) {
            // Creating schema
            $count      = 0;
            echo CLI::dotFill('creating schema', DOT_FILL);
            $old_schema = $manager->createSchema();
            echo CLI::color("done", green);
            echo "\n";

            // Initializing structure
            echo CLI::dotFill('initializing', DOT_FILL);
            $manager->initialize();
            echo CLI::color("done", green);
            echo "\n";

            if ($old_schema !== null) {
                echo CLI::h1('copy data', HEADER_FILL);
                $copier = new Repel\Adapter\Copier\DatabaseCopier($old_schema, $manager->db);
                $copier->copy();

                echo CLI::h1('initializing', HEADER_FILL);
                $initiator = new Initiator\Initiator($manager->db);

                if (key_exists("views_dir", $c)) {
                    echo CLI::dotFill('loading views', DOT_FILL);
                    $views = array_merge(glob($c['views_dir'] . "/**/*.sql"), glob($c['views_dir'] . "/*.sql"));

                    foreach ($views as $view) {
                        $initiator->addSource($view);
                    }

                    echo CLI::color("done", green);
                    echo "\n";
                }

                if (key_exists("functions_dir", $c)) {
                    echo CLI::dotFill('loading functions', DOT_FILL);
                    $functions = array_merge(glob($c['functions_dir'] . "/**/*.sql"), glob($c['functions_dir'] . "/*.sql"));

                    foreach ($functions as $function) {
                        $initiator->addSource($function);
                    }

                    echo CLI::color("done", green);
                    echo "\n";
                }

                if (key_exists("indexes_dir", $c)) {
                    echo CLI::dotFill('loading indexes', DOT_FILL);
                    $indexes = array_merge(glob($c['indexes_dir'] . "/**/*.sql"), glob($c['indexes_dir'] . "/*.sql"));

                    foreach ($indexes as $index) {
                        $initiator->addSource($index);
                    }

                    echo CLI::color("done", green);
                    echo "\n";
                }

                if (key_exists("triggers_dir", $c)) {
                    echo CLI::dotFill('loading triggers', DOT_FILL);
                    $triggers = array_merge(glob($c['triggers_dir'] . "/**/*.sql"), glob($c['triggers_dir'] . "/*.sql"));

                    foreach ($triggers as $trigger) {
                        $initiator->addSource($trigger);
                    }

                    echo CLI::color("done", green);
                    echo "\n";
                }

                if (key_exists("sequences_dir", $c)) {
                    echo CLI::dotFill('loading sequences', DOT_FILL);
                    $sequences = array_merge(glob($c['sequences_dir'] . "/**/*.sql"), glob($c['sequences_dir'] . "/*.sql"));

                    foreach ($sequences as $sequence) {
                        $initiator->addSource($sequence);
                    }

                    echo CLI::color("done", green);
                    echo "\n";
                }

                $initiator->initWithConnection();

                // Removing old schemas
                echo CLI::dotFill('removing backups', DOT_FILL);
                $manager->removeBackups();
                echo CLI::color("done", green);
                echo "\n";

                $drivers[] = $c["driver"];

                $result = $manager->db->exec('COMMIT;');
                if ($result === false) {
                    $errorInfo = $this->db->errorInfo();
                    throw new Exception('SQL ERROR: ' . "\n" . $errorInfo [2]);
                }
                echo CLI::color("SUCCESS", 'white', 'green');
                echo "\n";
            } else {
                throw new Exception("Błąd", $code, $previous);
            }
        }
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
