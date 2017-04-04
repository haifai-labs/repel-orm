<?php

namespace Repel\Initiator;

use Repel\Includes\CLI;
use Repel\Framework\DatabaseManager;
use Repel\Framework;
use Repel\Adapter;

class DatabaseUpdater extends RepelCli {

    private $db_config = null;

    public function __construct($db_config, $show_output = true) {
        $this->db_config = $db_config;

        parent::__construct($show_output);
    }

    public function update() {
        try {
            if ($this->db_config !== null) {
                if (!key_exists('databases', $this->db_config) || !Framework\RDatabaseConfigChecker::checkConfig($this->db_config["databases"])) {
                    $this->output(CLI::color("Incorrect database config file.", red));
                    return false;
                }
            } else {
                $this->output(CLI::color("No database config.", red));
                return false;
            }

            $drivers    = array();
            $manager    = null;
            $old_schema = null;

            foreach ($this->db_config['databases'] as $key => $c) {
                $this->output(CLI::h1('create default database', self::$HEADER_FILL));
                // Connecting
                $this->output(CLI::dotFill('connecting', self::$DOT_FILL));
                $manager = new DatabaseManager($c);
                $this->output(CLI::color("done", green));
                $this->output(CLI::newLine());


                if (!in_array($c["driver"], $drivers)) {
                    $result = $manager->db->exec('BEGIN;');
                    if ($result === false) {
                        $errorInfo = $this->db->errorInfo();
                        throw new Exception('SQL ERROR: ' . "\n" . $errorInfo [2]);
                    }

                    // Creating new schema
                    $this->output(CLI::dotFill('creating schema', self::$DOT_FILL));
                    $old_schema = $manager->createSchema();
                    $this->output(CLI::color("done", green));
                    $this->output(CLI::newLine());
                }

                // Initializing structure
                $this->output(CLI::dotFill('initializing', self::$DOT_FILL));
                $manager->initialize();
                $this->output(CLI::color("done", green));
                $this->output(CLI::newLine());

                if ($old_schema !== null) {
                    $this->output(CLI::h1('copy data', self::$HEADER_FILL));
                    $public_schema_adapter = new Adapter\Adapter($this->db_config['databases'], $c['adapter'], false);
                    $public_schema_adapter->addFetcher(new Adapter\Fetcher\PostgreSQLFetcher($key));
                    $public_schema_adapter->fetch($manager->db);

                    $old_config                      = $this->db_config['databases'];
                    $old_config[$key]['schema_name'] = $old_schema;

                    $old_schema_adapter = new Adapter\Adapter($old_config, $c['adapter'], false);
                    $old_schema_adapter->addFetcher(new Adapter\Fetcher\PostgreSQLFetcher($key));
                    $old_schema_adapter->fetch($manager->db);

                    $copier  = new Adapter\Copier\Copier($old_schema_adapter, $public_schema_adapter, $key, $key, false);
                    $queries = $copier->copy($manager->db);

                    $this->output(CLI::h1('initializing', self::$HEADER_FILL));
                    $initiator = new Initiator($manager->db);

                    $initiator->addQuery($queries);

                    if (key_exists("views_dir", $c)) {
                        $this->output(CLI::dotFill('loading views', self::$DOT_FILL));
                        $views = array_merge(glob($c['views_dir'] . "/**/*.sql"), glob($c['views_dir'] . "/*.sql"));

                        foreach ($views as $view) {
                            $initiator->addSource($view);
                        }

                        $this->output(CLI::color("done", green));
                        $this->output(CLI::newLine());
                    }

                    if (key_exists("functions_dir", $c)) {
                        $this->output(CLI::dotFill('loading functions', self::$DOT_FILL));
                        $functions = array_merge(glob($c['functions_dir'] . "/**/*.sql"), glob($c['functions_dir'] . "/*.sql"));

                        foreach ($functions as $function) {
                            $initiator->addSource($function);
                        }

                        $this->output(CLI::color("done", green));
                        $this->output(CLI::newLine());
                    }
                    if (key_exists("indexes_dir", $c)) {
                        $this->output(CLI::dotFill('loading indexes', self::$DOT_FILL));
                        $indexes = array_merge(glob($c['indexes_dir'] . "/**/*.sql"), glob($c['indexes_dir'] . "/*.sql"));

                        foreach ($indexes as $index) {
                            $initiator->addSource($index);
                        }

                        $this->output(CLI::color("done", green));
                        $this->output(CLI::newLine());
                    }
                    if (key_exists("triggers_dir", $c)) {
                        $this->output(CLI::dotFill('loading triggers', self::$DOT_FILL));
                        $triggers = array_merge(glob($c['triggers_dir'] . "/**/*.sql"), glob($c['triggers_dir'] . "/*.sql"));

                        foreach ($triggers as $trigger) {
                            $initiator->addSource($trigger);
                        }

                        $this->output(CLI::color("done", green));
                        $this->output(CLI::newLine());
                    }
                    if (key_exists("sequences_dir", $c)) {
                        $this->output(CLI::dotFill('loading sequences', self::$DOT_FILL));
                        $sequences = array_merge(glob($c['sequences_dir'] . "/**/*.sql"), glob($c['sequences_dir'] . "/*.sql"));

                        foreach ($sequences as $sequence) {
                            $initiator->addSource($sequence);
                        }

                        $this->output(CLI::color("done", green));
                        $this->output(CLI::newLine());
                    }

                    if (key_exists("update_dir", $c)) {
                        $this->output(CLI::dotFill('loading update', self::$DOT_FILL));
                        $views = array_merge(glob($c['update_dir'] . "/**/*.sql"), glob($c['update_dir'] . "/*.sql"));

                        foreach ($views as $view) {
                            $initiator->addSource($view);
                        }

                        $this->output(CLI::color("done", green));
                        $this->output(CLI::newLine());
                    }
                    $initiator->initWithConnection();
                    // Removing old schemas
                    $this->output(CLI::dotFill('removing backups', self::$DOT_FILL));
                    $manager->removeBackups();
                    $this->output(CLI::color("done", green));
                    $this->output(CLI::newLine());

                    if (!in_array($c["driver"], $drivers)) {
                        $result = $manager->db->exec('COMMIT;');
                    }

                    $drivers[] = $c["driver"];

                    if ($result === false) {
                        $errorInfo = $this->db->errorInfo();
                        throw new Exception('SQL ERROR: ' . "\n" . $errorInfo [2]);
                    }
                    $this->output(CLI::color("SUCCESS", 'white', 'green'));
                    $this->output(CLI::newLine());
                } else {
                    throw new Exception("Błąd", $code, $previous);
                }
            }

            return true;
        } catch (\Exception $ex) {
            $this->output(CLI::failure($ex));
            return false;
        }
    }

}

?>
