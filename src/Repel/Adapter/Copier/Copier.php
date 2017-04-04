<?php

namespace Repel\Adapter\Copier;

class Copier extends \Repel\Initiator\RepelCli {

    private $source;
    private $target;
    private $source_key;
    private $target_key;
    public $tables;
    public $query;

    public function __construct($source, $target, $source_key, $target_key, $show_output = true) {
        $this->source     = $source;
        $this->target     = $target;
        $this->source_key = $source_key;
        $this->target_key = $target_key;
        $this->tables     = array();
        $this->query      = "";

        parent::__construct($show_output);
    }

    public function copy() {
        $this->getInnerJoinSchema();

        $source_schema = $this->source->config[$this->source_key]['schema_name'];
        $target_schema = $this->target->config[$this->target_key]['schema_name'];


        foreach ($this->tables as $t) {
            $this->output($t->name . "\n");
            $query = "ALTER TABLE {$target_schema}.{$t->name} DISABLE TRIGGER ALL; INSERT INTO " . $target_schema . "." . $t->name . "(\"";

            $columns = implode("\",\"", $t->columns);

            $query .= $columns . "\") SELECT \"" . $columns . "\" FROM " . $source_schema . "." . $t->name . "; ALTER TABLE {$target_schema}.{$t->name} ENABLE TRIGGER ALL;";

            $this->query[] .= $query;
        }

        return $this->query;
    }

    private function getInnerJoinSchema() {
        foreach ($this->source->getSchemaTables2($this->source_key) as $source_table) {
            if ($source_table->type === 'table') {
                foreach ($this->target->getSchemaTables2($this->target_key) as $target_table) {
                    if ($source_table->name === $target_table->name) {
                        $columns = $this->innerJoinTables($source_table, $target_table);

                        $table          = new \Repel\Adapter\Classes\Table();
                        $table->name    = $target_table->name;
                        $table->columns = $columns;

                        $this->tables[] = $table;

                        break;
                    }
                }
            }
        }
    }

    private function innerJoinTables($source, $target) {
        $innerJoin = array();

        foreach ($source->columns as $source_column) {
            foreach ($target->columns as $target_column) {
                if ($source_column->name === $target_column->name && $source_column->type === $target_column->type) {
                    $innerJoin[] = $target_column->name;
                    break;
                }
            }
        }

        return $innerJoin;
    }

}
