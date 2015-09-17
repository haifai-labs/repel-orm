<?php

namespace Repel\Initiator;

use Repel\Includes\CLI;
use Repel\Initiator\Classes;
use Repel\Framework;

class Initiator {

    private $database;
    private $sources;

    public function __construct($database) {
        $this->sources  = array();
        $this->database = $database;
    }

    public function addSource($file) {
        if (file_exists($file)) {
            $source = new Classes\Source($file);

            $this->sources[] = $source;
        }

        return $this;
    }

    public function init() {
        $manager = new Framework\DatabaseManager($this->database);

        $result = $manager->db->exec('BEGIN;');
        if ($result === false) {
            $errorInfo = $manager->db->errorInfo();
            throw new \Exception('SQL ERROR: ' . "\n" . $errorInfo [2]);
        }

        foreach ($this->sources as $source) {
            echo $source->file_path . "...";
            if (strlen(trim($source->file_content))) {
                $res = $manager->db->exec($source->file_content);
                if ($res === false) {
                    $errorInfo = $manager->db->errorInfo();
                    throw new \Exception('SQL ERROR: ' . "\n" . $errorInfo [2]);
                }
                echo CLI::color("done", green);
                echo "\n";
            } else {
                echo " empty.\n";
            }
        }

        $result = $manager->db->exec('COMMIT;');
    }

    public function initWithConnection() {
        foreach ($this->sources as $source) {
            echo $source->file_path . "...";
            if (strlen(trim($source->file_content))) {
                $res = $this->database->exec($source->file_content);
                if ($res === false) {
                    $errorInfo = $this->database->errorInfo();
                    throw new \Exception('SQL ERROR: ' . "\n" . $errorInfo [2]);
                }
                echo CLI::color("done", green);
                echo "\n";
            } else {
                echo " empty.\n";
            }
        }
    }

}
