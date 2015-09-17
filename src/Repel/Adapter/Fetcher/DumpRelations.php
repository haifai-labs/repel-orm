<?php

namespace Repel\Adapter\Fetcher;

class DumpRelations {

    protected $types_cache;
    protected $query_cache;
    protected $db;
    protected $schema;

    public function __construct($schema_name, $connection) {
        $this->types_cache = array();
        $this->query_cache = array();
        $this->db          = $connection;
        $this->schema      = "'" . $schema_name . "'";
    }

    protected function getPreparedQuery($q) {
        if (isset($this->query_cache[$q])) {
            return $this->query_cache[$q];
        }

        $p                     = $this->db->prepare($q);
        $this->query_cache[$q] = $p;
        return $p;
    }

    public function dumpRelations() {

        $p = $this->getPreparedQuery("select pg_class.oid as reloid, * from pg_class " .
                "join pg_namespace on ( pg_class.relnamespace = pg_namespace.oid ) " .
                "where relkind in ( 'r' ) and nspname in ( {$this->schema} ) " .
                "order by relname asc");

        $p->execute();

        $res = array();

        foreach ($p->fetchAll() as $t) {
            $tabs               = $this->getRelationships($t);
            $res[$t['relname']] = $tabs;
        }

        return $res;
    }

    protected function getRelationships($t) {
        $q = "select fk_tab.relname as fk_tab,
			fk_att.attname as fk_att,
			fk_tab.oid as fk_oid,
			pk_tab.relname as pk_tab,
			pk_att.attname as pk_att,
			pk_tab.oid as pk_oid
			from pg_constraint 
			left join pg_class fk_tab on ( pg_constraint.conrelid = fk_tab.oid )
			left join pg_class pk_tab on ( pg_constraint.confrelid = pk_tab.oid )
			left join pg_attribute fk_att on ( pg_constraint.conkey[1] = fk_att.attnum AND fk_tab.oid = fk_att.attrelid )
			left join pg_attribute pk_att on ( pg_constraint.confkey[1] = pk_att.attnum AND pk_tab.oid = pk_att.attrelid )

			where pg_constraint.contype = 'f'
			and :oid IN ( fk_tab.oid, pk_tab.oid )
			order by fk_att.attnum asc ";

        $p = $this->getPreparedQuery($q);

        $p->execute(array('oid' => $t['reloid']));

        $tabs = array();

        foreach ($p->fetchAll() as $con) {

            if ($con['pk_oid'] == $t['reloid'] && $con['fk_oid'] != $t['reloid']) {
                //has many
            }

            if ($con['pk_oid'] != $t['reloid'] && $con['fk_oid'] == $t['reloid']) {
                //belongs to
                if (!in_array($con['pk_tab'], $tabs)) {
                    $tabs[] = $con['pk_tab'];
                }
            }

            if ($con['pk_oid'] == $t['reloid'] && $con['fk_oid'] == $t['reloid']) {
                //self belongs to
            }
        }

        return $tabs;
    }

    public function dumpAll() {
        $this->dumpRelations();
    }

}
