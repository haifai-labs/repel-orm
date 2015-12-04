#!/usr/bin/env php
<?php
require dirname(__DIR__) . '/autoloader.php';

use Repel\Includes\CLI;
use Repel\Initiator;
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

    foreach ($config['databases'] as $key => $config) {
        $initiator = new Initiator\Initiator($config);

        echo CLI::h1('initialize database', HEADER_FILL);
        echo CLI::h2('add sources', HEADER_FILL);

        if (key_exists("views_dir", $config)) {
            echo CLI::dotFill('loading views', DOT_FILL);
            $views = array_merge(glob($config['views_dir'] . DIRECTORY_SEPARATOR . "**" . DIRECTORY_SEPARATOR . "*.sql"), glob($config['views_dir'] . DIRECTORY_SEPARATOR . "*.sql"));

            foreach ($views as $view) {
                $initiator->addSource($view);
            }

            echo CLI::color("done", green);
            echo "\n";
        }

        if (key_exists("functions_dir", $config)) {
            echo CLI::dotFill('loading functions', DOT_FILL);
            $functions = array_merge(glob($config['functions_dir'] . DIRECTORY_SEPARATOR . "**" . DIRECTORY_SEPARATOR . "*.sql"), glob($config['functions_dir'] . DIRECTORY_SEPARATOR . "*.sql"));

            foreach ($functions as $function) {
                $initiator->addSource($function);
            }

            echo CLI::color("done", green);
            echo "\n";
        }

        if (key_exists("indexes_dir", $config)) {
            echo CLI::dotFill('loading indexes', DOT_FILL);
            $indexes = array_merge(glob($config['indexes_dir'] . DIRECTORY_SEPARATOR . "**" . DIRECTORY_SEPARATOR . "*.sql"), glob($config['indexes_dir'] . DIRECTORY_SEPARATOR . "*.sql"));

            foreach ($indexes as $index) {
                $initiator->addSource($index);
            }

            echo CLI::color("done", green);
            echo "\n";
        }

        if (key_exists("triggers_dir", $config)) {
            echo CLI::dotFill('loading triggers', DOT_FILL);
            $triggers = array_merge(glob($config['triggers_dir'] . DIRECTORY_SEPARATOR . "**" . DIRECTORY_SEPARATOR . "*.sql"), glob($config['triggers_dir'] . DIRECTORY_SEPARATOR . "*.sql"));

            foreach ($triggers as $trigger) {
                $initiator->addSource($trigger);
            }

            echo CLI::color("done", green);
            echo "\n";
        }

        if (key_exists("inserts_dir", $config)) {
            echo CLI::dotFill('loading inserts', DOT_FILL);
            if (is_array($config['inserts_dir'])) {
                $inserts = array();

                foreach ($config['inserts_dir'] as $insert) {
                    $inserts = array_merge($inserts, glob($insert . DIRECTORY_SEPARATOR . "**" . DIRECTORY_SEPARATOR . "*.sql"), glob($insert . DIRECTORY_SEPARATOR . "*.sql"));
                }
            } else {
                $inserts = array_merge(glob($config['inserts_dir'] . DIRECTORY_SEPARATOR . "**" . DIRECTORY_SEPARATOR . "*.sql"), glob($config['inserts_dir'] . DIRECTORY_SEPARATOR . "*.sql"));
            }

            foreach ($inserts as $insert) {
                $initiator->addSource($insert);
            }

            echo CLI::color("done", green);
            echo "\n";
        }

        if (key_exists("sequences_dir", $config)) {
            echo CLI::dotFill('loading sequences', DOT_FILL);
            $sequences = array_merge(glob($config['sequences_dir'] . "/**/*.sql"), glob($config['sequences_dir'] . "/*.sql"));

            foreach ($sequences as $sequence) {
                $initiator->addSource($sequence);
            }

            echo CLI::color("done", green);
            echo "\n";
        }
        // @todo: modyfikatory
        $initiator->init();

        echo CLI::success();
    }
} catch (Exception $ex) {
    echo CLI::failure($ex);
    die();
}
