<?php

namespace Repel\Framework;

class RServiceContainer {

    protected $adapters = array();
    protected $adapterClasses = array();
    protected $connectionManagers = array();
    protected $defaultDatasource = "";
    protected $formatters = array();

    public function setAdapterClass($name, $adapterClass) {
        $this->adapterClasses[$name] = $adapterClass;
        unset($this->adapters[$name]);
    }

    public function setAdapterClasses($adapterClasses) {
        $this->adapterClasses = $adapterClasses;
        $this->adapters = array();
    }

    public function setConnectionManager($name, $manager) {
        if (isset($this->connectionManagers[$name])) {
            $this->connectionManagers[$name]->closeConnections();
        }
        if (!$manager->getName()) {
            $manager->setName($name);
        }
        $this->connectionManagers[$name] = $manager;
    }

    public function getConnectionManager($name) {
        if (!isset($this->connectionManagers[$name])) {
            die("nie ma connectora");
        }

        return $this->connectionManagers[$name];
    }

    public function hasConnectionManager($name) {
        return isset($this->connectionManagers[$name]);
    }

    public function getConnectionManagers() {
        return $this->connectionManagers;
    }

    public function setDefaultDatasource($defaultDatasource) {
        $this->defaultDatasource = $defaultDatasource;
    }

    public function addFormatter($name, $formatter) {
        $this->formatters[$name] = $formatter;
    }

    public function getFormatter($name) {
        if (key_exists($name, $this->formatters)) {
            return $this->formatters[$name];
        } else {
            return null;
        }
    }

    public function getFormatters() {
        return $this->formatters;
    }

}
