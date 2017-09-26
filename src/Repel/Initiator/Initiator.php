<?php

namespace Repel\Initiator;

use Repel\Includes\CLI;
use Repel\Initiator\Classes;
use Repel\Framework;

class Initiator {

    private $database;
    private $sources;
    private $queries;

    public function __construct($database,$config = null) {
        $this->sources  = array();
        $this->database = $database;
        $this->config = $config;
        $this->queries  = "";
    }

    public function addSource($file) {
        if (file_exists($file)) {
            $source = new Classes\Source($file);

            $this->sources[] = $source;
        }

        return $this;
    }

    public function addQuery($query) {
        if (is_array($query)) {
            $this->queries .= implode("", $query);
        } else {
            $this->queries .= $query;
        }

        return $this;
    }

    public function init() {
        $manager = new Framework\DatabaseManager($this->database,$this->config);
        $result = $manager->db->exec('BEGIN;');
        if ($result === false) {
            $errorInfo = $manager->db->errorInfo();
            throw new \Exception('SQL ERROR: ' . "\n" . $errorInfo [2]);
        }

        if (strlen($this->queries)) {

            CLI::out("queries...");
            $res = $manager->db->exec($this->queries);
            if ($res === false) {
                $errorInfo = $manager->db->errorInfo();
                throw new \Exception('SQL ERROR: ' . "\n" . $errorInfo [2]);
            }
            CLI::out(CLI::color("done", green));
            CLI::out("\n");
        }

        foreach ($this->sources as $source) {
            CLI::out($source->file_path . "...");
            if (strlen(trim($source->file_content))) {
                $res = $manager->db->exec($source->file_content);
                if ($res === false) {
                    $errorInfo = $manager->db->errorInfo();
                    throw new \Exception('SQL ERROR: ' . "\n" . $errorInfo [2]);
                }
                CLI::out(CLI::color("done", green));
                CLI::out("\n");
            } else {
                CLI::out(" empty.\n");
            }
        }

        $result = $manager->db->exec('COMMIT;');
    }

    public function initWithConnection() {
        if (strlen($this->queries)) {
            CLI::out("queries...");
            $res = $this->database->exec($this->queries);
            if ($res === false) {
                $errorInfo = $this->database->errorInfo();
                throw new \Exception('SQL ERROR: ' . "\n" . $errorInfo [2]);
            }
            CLI::out(CLI::color("done", green));
            CLI::out("\n");
        }

        foreach ($this->sources as $source) {
            CLI::out($source->file_path . "...");
            if (strlen(trim($source->file_content))) {
                $res = $this->database->exec($source->file_content);
                if ($res === false) {
                    $errorInfo = $this->database->errorInfo();
                    throw new \Exception('SQL ERROR: ' . "\n" . $errorInfo [2]);
                }
                CLI::out(CLI::color("done", green));
                CLI::out("\n");
            } else {
                CLI::out(" empty.\n");
            }
        }
    }

}
