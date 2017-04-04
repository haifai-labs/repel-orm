<?php

namespace Repel\Initiator;

use Repel\Includes\CLI;
use Repel\Framework\DatabaseManager;
use Repel\Framework;

class DatabaseCreator extends RepelCli {

    private $db_config = null;

    public function __construct($db_config, $show_output = true) {
        $this->db_config = $db_config;

        parent::__construct($show_output);
    }

    public function create() {
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

            $drivers = array();

            foreach ($this->db_config['databases'] as $key => $c) {
                $this->output(CLI::h1('create default database', self::$HEADER_FILL));
                // Connecting
                $this->output(CLI::dotFill('connecting', self::$DOT_FILL));
                $manager = new DatabaseManager($c);
                $this->output(CLI::color("done", green));
                $this->output(CLI::newLine());

                if ($manager->db->exec('BEGIN;') === false) {
                    $errorInfo = $this->db->errorInfo();
                    throw new \Exception('SQL ERROR: ' . "\n" . $errorInfo [2]);
                }

                if (!in_array($c["driver"], $drivers)) {
                    // Creating schema
                    $this->output(CLI::dotFill('creating schema', self::$DOT_FILL));
                    $manager->createSchema();
                    $this->output(CLI::color("done", green));
                    $this->output(CLI::newLine());
                }

                // Initializing structure
                $this->output(CLI::dotFill('initializing', self::$DOT_FILL));
                $manager->initialize();
                $this->output(CLI::color("done", green));
                $this->output(CLI::newLine());

                if (!in_array($c["driver"], $drivers)) {
                    // Removing old schemas
                    $this->output(CLI::dotFill('removing backups', self::$DOT_FILL));
                    $manager->removeBackups();
                    $this->output(CLI::color("done", green));
                    $this->output(CLI::newLine());

                    $drivers[] = $c["driver"];
                }

                if ($manager->db->exec('COMMIT;') === false) {
                    $errorInfo = $this->db->errorInfo();
                    throw new \Exception('SQL ERROR: ' . "\n" . $errorInfo [2]);
                }
                $this->output(CLI::color("SUCCESS", 'white', 'green'));
                $this->output(CLI::newLine());
            }
            return true;
        } catch (\Exception $e) {
            if (isset($manager->db)) {
                $manager->db->exec('ROLLBACK;');
            }
            $this->output(CLI::color("failed", red));
            $this->output(CLI::newLine());
            $this->output(CLI::color($e->getMessage(), 'white', 'red'));
            $this->output(CLI::newLine());
            return false;
        }
    }

}

?>
