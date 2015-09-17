<?php

// Copies schema $schema to 'public'

namespace Repel\Adapter\Copier;

use Repel\Includes\CLI;

class DatabaseCopier {

    protected $db;
    protected $schema;

    /**
     * 
     * @param type $schema - schema name
     * @param type $connection - PDO connection
     */
    public function __construct($schema, $connection) {
        $this->db     = $connection;
        $this->schema = $schema;
    }

    /**
     * Copy all the tables from the schema $schema, one after another
     */
    public function copy() {
        // get all tables from $schema (only if exists in 'public' schema)
        $sql = "SELECT t1.tablename AS tablename FROM pg_tables t1 JOIN pg_tables t2 ON ( t1.tablename = t2.tablename ) WHERE t1.schemaname = '{$this->schema}' AND t2.schemaname = 'public' ORDER BY t1.tablename;";

        $tables = $this->db->query($sql);

        if (count($tables)) {
            foreach ($tables as $table) {
                $this->copyTable($table[0]);
            }
        } else {
            throw new \Exception("Nothing to copy");
        }
    }

    /**
     * Copy single table
     * 
     * @param type $table
     */
    public function copyTable($table) {
        // disable triggers
        echo CLI::dotFill('copy ' . $table, DOT_FILL);
        $res = $this->db->query("ALTER TABLE public.{$table} DISABLE TRIGGER ALL;");

        if (!$res) {
            echo CLI::color("fail", red);
            throw new \Exception("Error during disable triggers");
        }

        $columns = array();
        $values  = array();

        $q = "SELECT c1.column_name AS column "
                . ", ( c1.is_nullable = 'YES' AND c2.is_nullable = 'NO' )::integer as became_not_null"
                . ", c1.data_type as old_data_type"
                . ", c2.data_type as data_type"
                . ", c2.column_default as column_default"
                . " FROM information_schema.columns c1 "
                . "JOIN information_schema.columns c2 ON ( c1.table_name = c2.table_name AND c1.column_name = c2.column_name ) "
                . "WHERE c1.table_name = '{$table}' AND c1.table_schema = '{$this->schema}' AND c2.table_schema = 'public';";
                
        $res = $this->db->query($q);

        if (!$res) {
            echo CLI::color("fail", red);
            throw new \Exception("Error during select columns");
        }

        foreach ($res as $row) {
            $columns[] = '"' . $row['column'] . '"';
            $val       = '"' . $row['column'] . '"';
            if ($row['old_data_type'] != $row['data_type']) {
                $val.='::' . $row['data_type'];
            }
            if ($row['became_not_null'] > 0) {
                $coalesce = $row['column_default'];

                if (strlen($coalesce) <= 0) {
                    $coalesce = "''";
                    if ($row['data_type'] == 'integer') {
                        $coalesce = "0";
                    }
                    if ($row['data_type'] == 'numeric') {
                        $coalesce = "0";
                    }
                    if ($row['data_type'] == 'tsvector') {
                        $coalesce = "to_tsvector(''::text)";
                    }
                }

                $val = 'COALESCE( ' . $val . ', ' . $coalesce . ' )';
            }

            $values[] = $val;
        }

        $q = "SELECT column_name, data_type FROM information_schema.columns c WHERE table_schema = 'public' AND table_name = '{$table}' "
                . "AND NOT EXISTS ( SELECT * FROM information_schema.columns WHERE table_schema = '{$this->schema}' AND table_name = '{$table}' AND column_name = c.column_name  ) "
                . "AND is_nullable = 'NO' AND column_default IS NULL;";

        $res = $this->db->query($q);

        if (!$res) {
            echo CLI::color("fail", red);
            throw new \Exception("Error during select data types");
        }

        foreach ($res as $row) {
            $type  = $row['data_type'];
            $value = "''";
            if ($type == 'integer') {
                $value = "0";
            }
            if ($type == 'numeric') {
                $value = "0";
            }
            if ($type == 'tsvector') {
                $value = "to_tsvector(''::text)";
            }

            $columns[] = '"' . $row['column_name'] . '"';
            $values[]  = $value;
        }

        $columns_list = implode(", ", $columns);
        $values_list  = implode(", ", $values);

        $q = "INSERT INTO public.{$table} ( {$columns_list} ) SELECT {$values_list} FROM {$this->schema}.{$table};";

        $res = $this->db->query($q);

        if (!$res) {
            echo CLI::color("fail", red);
            throw new \Exception("Error during insert");
        }

        $res = $this->db->query("ALTER TABLE public.{$table} ENABLE TRIGGER ALL;");

        if (!$res) {
            echo CLI::color("fail", red);
            throw new \Exception("Error during enable trigger");
        }

        echo CLI::color("done", green);
        echo "\n";
    }

}
