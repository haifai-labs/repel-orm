<?php

namespace Repel\Framework;

class RConnectionManagerSingle {

    protected $name;
    protected $configuration = array();
    protected $connection    = null;

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function getConfiguration() {
        return $this->configuration;
    }

    public function setConnection($connection) {
        $this->setConfiguration(null);
        $this->connection = $connection;
    }

    public function setConfiguration($configuration) {
        $this->configuration = $configuration;
        $this->closeConnections();
    }

    public function getConnection() {
        if ($this->connection === null) {
            $this->connection = RDbConnection::instance($this->configuration['driver'], $this->configuration['username'], $this->configuration['password']);
        }

        return $this->connection;
    }

    public function getDatabase() {
        return $this->configuration["database"];
    }

    public function closeConnections() {
        $this->connection = null;
    }

}
