<?php

namespace Repel\Adapter\Classes;

class Table {

    public $name;
    public $type;
    public $columns = array();
    public $relationships = array();

    /**
     * 
     * @param type $table_name
     * @return boolean
     */
    public function getReferenceTo($table_name) {
        foreach ($this->columns as $column) {
            if ($column->foreign_key && $column->foreign_key->referenced_table === $table_name) {
                return array('column' => $column->name, 'foreign_key' => $column->foreign_key);
            }
        }
        return false;
    }

    public function hasReferenceTo($table_name) {
        foreach ($this->columns as $column) {
            if ($column->foreign_key && $column->foreign_key->referenced_table === $table_name) {
                return true;
            }
        }
        return false;
    }

    public function removeRelationship($table, $column = null) {
        foreach ($this->relationships as $krelationship => $relationship) {
            if ($column === null) {
                if ($relationship->table === $table) {
                    array_splice($this->relationships, $krelationship, 1);
                }
            } else {
                // Match $column also
            }
        }
    }

    public function addRelationship(Relationship $relationship) {
        $this->relationships[] = $relationship;
    }

}
