<?php

namespace Repel\Framework;

class DatabaseManager {

    public $db;
    public $config;

    public function __construct($connectionInfo, $config = null) {
      if ($connectionInfo instanceof \Repel\Framework\RConnectionManagerSingle) {
          $connection   = $connectionInfo->getConnection();
          $this->db     = $connection->get();
          $this->config = $config;
          if ($this->db === null){
              $this->connect();
          }
      } else if ($connectionInfo instanceof Repel\Framework\RDbConnection) {
          $this->db     = $connectionInfo->get();
          $this->config = $config;
      } else {
          $this->config = $connectionInfo;
          $this->connect();
      }
    }

    public function connect() {
        $this->db = new \PDO($this->config ['driver'], $this->config ['username'], $this->config ['password']);
    }

    public function createSchema() {
        $sql = "SELECT count(schema_name) as count FROM information_schema.schemata WHERE schema_name = 'public';";
        foreach ($this->db->query($sql) as $row) {
            $count = $row ['count'];
        }

        $old_name = 'zzz_old_' . time();

        if ($count) {
            $result = $this->db->exec('ALTER SCHEMA public RENAME TO ' . $old_name);
            if ($result === false) {
                $errorInfo = $this->db->errorInfo();
                throw new \Exception('SQL ERROR: ' . "\n" . $errorInfo [2]);
            }
        }
        $sql = "CREATE SCHEMA public";

        $result = $this->db->exec($sql);
        if ($result === false) {
            $errorInfo = $this->db->errorInfo();
            throw new \Exception('SQL ERROR: ' . "\n" . $errorInfo [2]);
        } else {
            return $old_name;
        }
    }


    public function clearSchema(){
        // $sql = "SELECT tablename FROM pg_tables WHERE schemaname = 'public'";
        // $errorInfo = $this->db->errorInfo();
        // // var_dump($errorInfo);
        // foreach ($this->db->query($sql) as $row) {
        //     var_dump($row['tablename']);
        // }
        //@TODO clear
    }

    public function initialize() {
        if (!file_exists($this->config ['schema'])) {
            throw new \Exception("Schema file('" . $this->config ['schema'] . "') is missing.");
        }
        $schema = file_get_contents($this->config ['schema']);
        $result = $this->db->exec($schema);
        if ($result === false) {
            $errorInfo = $this->db->errorInfo();
            throw new \Exception('SQL ERROR: ' . "\n" . $errorInfo [2]);
        }
    }

    public function removeBackups() {
        $sql     = "SELECT schema_name FROM information_schema.schemata WHERE schema_name LIKE 'zzz_old_%';";
        $backups = array();
        foreach ($this->db->query($sql) as $row) {
            preg_match("/zzz_old_(.*)/", $row ['schema_name'], $matches);
            $timestamp            = $matches [1];
            $backups [$timestamp] = $row ['schema_name'];
            krsort($backups, SORT_NUMERIC);
        }
        if (count($backups) > 3) {
            $i = 1;
            foreach ($backups as $backup) {
                if ($i > 2 && $i !== count($backups) - 1) {
                    $result = $this->db->exec('DROP SCHEMA ' . $backup . ' CASCADE');
                    if ($result === false) {
                        $errorInfo = $this->db->errorInfo();
                        throw new \Exception('SQL ERROR: ' . "\n" . $errorInfo [2]);
                    }
                }
                $i ++;
            }
        }
    }

    public function execute($query) {
        if (strlen($query) > 0) {
            $result = $this->db->exec($query);
            if ($result === false) {
                $errorInfo = $this->db->errorInfo();
                throw new \Exception('SQL ERROR: ' . "\n" . $errorInfo [2]);
            }
            return $result;
        }
    }

}
