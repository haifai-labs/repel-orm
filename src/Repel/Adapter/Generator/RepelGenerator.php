<?php

namespace Repel\Adapter\Generator;

use Repel\Adapter\Generator\BaseGenerator;
use Repel\Includes\CLI;

const DOT_FILL    = 36;
const HEADER_FILL = 38;

class RepelGenerator extends BaseGenerator {

    private $table_name       = "";
    private $primary_keys     = array();
    private $foreign_keys     = array();
    protected $adapter        = null;
    private $cross_reference  = false;
    private $key              = null;
    private $base_path        = null;
    private $namespace_prefix = null;

    public function __construct($config, $key) {
        $model_path             = $config['model_directory_path'];
        $this->namespace_prefix = $config['model_namespace_prefix'];

        if ($model_path) {
            if ($model_path[strlen($model_path) - 1] !== '/') {
                $model_path .= '/';
            }
            $this->model_path = $model_path;
        } else {
            $this->model_path = __DIR__ . '/data/';
        }
        $this->base_path = $this->model_path . 'Base/';
        $this->key       = $key;
    }

    public function setAdapter($adapter) {
        if (get_class($adapter) === 'Repel\Adapter\Adapter') {
            $this->adapter = $adapter;
        } else {
            throw new \Exception('[RepelGenerator] wrong adapter instance given.');
        }
    }

    public static function getTableName($name) {
        return 'D' . self::singular($name);
    }

    public static function getTableBaseName($name) {
        return 'R' . self::singular($name) . 'Base';
    }

    public static function getQueryName($name) {
        return 'D' . self::singular($name) . 'Query';
    }

    public static function getQueryBaseName($name) {
        return 'R' . self::singular($name) . 'QueryBase';
    }

    public function generate() {
        $warning = false;

        if (!is_dir($this->model_path)) {
            throw new \Exception("[RepelGenerator] Given model path does not exist, or is not a directory! ({$this->model_path})");
        }
        if (!is_dir($this->base_path)) {
            mkdir($this->base_path);
        } else {
            // Check if proper directory
            $dh       = opendir($this->base_path);
            $ignore   = array('.', '..');
            $warning  = false;
            while (false !== ($filename = readdir($dh))) {
                if (in_array($filename, $ignore)) {
                    continue;
                }
                if (preg_match('/^R.*Base.php$/', $filename)) {
                    unlink($this->base_path . $filename);
                } else {
                    $warning = true;
                }
            }
        }

        if ($warning) {
            echo CLI::warning("Warning! Irrelevant files found in base_path!");
        }
        foreach ($this->adapter->getTables() as $table) {
            echo CLI::dotFill($table->name . ' (' . CLI::color($table->type, dark_gray) . ')', DOT_FILL + 11);

            $this->clear();

            $table_filename      = self::getTableName($table->name);
            $query_filename      = self::getQueryName($table->name);
            $table_base_filename = self::getTableBaseName($table->name);
            $query_base_filename = self::getQueryBaseName($table->name);

            if (!file_exists($this->model_path . $table_filename . '.php')) {
                file_put_contents($this->model_path . $table_filename . '.php', $this->generateTable($table));
            }
            if (!file_exists($this->model_path . $query_filename . '.php')) {
                file_put_contents($this->model_path . $query_filename . '.php', $this->generateTableQuery($table));
            }

            file_put_contents($this->base_path . $table_base_filename . '.php', $this->generateTableBase($table));
            file_put_contents($this->base_path . $query_base_filename . '.php', $this->generateTableQueryBase($table));

            echo CLI::color("done", green) . "\n";
        }
    }

    public function clear() {
        $this->foreign_keys    = array();
        $this->table_name      = "";
        $this->cross_reference = false;
    }

    public function generateMapField($column){
        $result ='    ';
        if ($column->name==="password" || ($column->name==="deleted" && ($column->type === 'int' || $column->type=== 'integer'))){
            $result .= '"^'. $column->name .'" => "'. self::camelCase($column->name) .'",' . "\n";
            return $result;
        }
        switch ($column->type) {
            case 'time':
            case 'time with time zone':
            case 'time without time zone':
                $result .= '"'. $column->name .'" => array("'. self::camelCase($column->name).':date" => "H:i"),' . "\n";
                break;
            case 'timestamp':
            case 'timestamp with time zone':
            case 'timestamp without time zone':
            case 'date':
                $result .= '"'. $column->name .'" => array("'. self::camelCase($column->name).':date" => "Y-m-d"),' . "\n";
                break;
            default:
                $result .= '"'. $column->name .'" => "'. self::camelCase($column->name) .'",' . "\n";
                break;
        }
        return $result;

    }
    public function generateTable($table) {
        $namespace_prefix = $this->namespace_prefix;

        $result = "<?php" . "\n";
        $result .= "namespace {$namespace_prefix}data;";
        $result .= "\n";
        $result .= "use {$namespace_prefix}data\Base;";

        $result .= "\n\nclass " . self::getTableName($table->name) . " extends Base\\" . self::getTableBaseName($table->name) . " {\n\n";
        $result .= "public static \$MAP = array(\n";
        foreach ($table->columns as $column) {
            $result.= $this->generateMapField($column);
        }
        $result .= ");\n";
            //
            //     "birth_date" => array("birthDate:date" => "Y-m-d"),
            //     "pay"        => array("pay:decimal"=>""),
            //     "work_status*"        => array("workStatusDescription:dict"=>"WORK_STATUSES")
            //
            // "active" => array("active:bool" => false), // toString === false
            // "^deleted" => "deleted",
            // "^password" => "password"

        $result .= "";
        $result .= "\n\n}\n\n";
        return $result;
    }

    public function generateTableQuery($table) {
        $namespace_prefix = $this->namespace_prefix;

        $result = "<?php" . "\n";
        $result .= "namespace {$namespace_prefix}data;";
        $result .= "\n";
        $result .= "use {$namespace_prefix}data\Base;";
        $result .= "\n\nclass " . self::getQueryName($table->name) . " extends Base\\" . self::getQueryBaseName($table->name) . " {";
        $result .= "\n\n}\n\n";
        return $result;
    }

    public function generateTableBase($table) {
        $namespace_prefix = $this->namespace_prefix;

        $this->table_name = $table->name;
        $result           = "<?php" . "\n\n";
        $result .= "namespace {$namespace_prefix}data\Base;\n";
        $result .= "\n";
        $result .= "use {$namespace_prefix}data;\n";
        $result .= "use Repel\Framework;\n\n";
        $result .= "class " . self::getTableBaseName($table->name) . " extends Framework\RActiveRecord {\n\n";

        $result .= "\tpublic \$_DATABASE = \"{$this->key}\";\n";
        $result.= "\tpublic \$ADAPTER = \"{$this->adapter->adapter}\";\n";
        $result.= "\tpublic \$TABLE = \"{$table->name}\";\n\n";

        $result .= $this->generateColumnTypesArray($table->columns);
        $result.="\n\n";

        $result .= $this->generateAutoIncrementArray($table->columns);
        $result.="\n\n";

        $result .= $this->generatePrimaryKeysArray($table->columns);
        $result.="\n\n";

        $result .= $this->generateDefaultArray($table->columns);
        $result.="\n\n";

        $result .= $this->generateObjectProperties($table->columns);
        $result.="\n";

        if (count($this->foreign_keys)) {
            $result .= $this->generateForeignKeyObjects();
            $result.="\n";
        }

        if (count($table->relationships)) {
            $result .= $this->generateRelationshipObjects($table->relationships);

            if ($this->cross_reference) {
                $result .= "\t// relationship object\n";
                $result .= "\tprivate \$_relationship = null;\n";
            }
            $result.="\n";
        }

        if (count($this->foreign_keys)) {
            $result .= $this->generateForeignKeyMethods();
        }

        if (count($table->relationships)) {
            $result .= $this->generateRelationshipMethods($table->relationships);

            if ($this->cross_reference) {
                $result .= "\tpublic function setRelationship(\$relationship) {\n";
                $result .= "\t\treturn \$this->_relationship = \$relationship;\n";
                $result .= "\t}\n";
                $result .= "\n";
                $result .= "\tpublic function getRelationship() {\n";
                $result .= "\t\treturn \$this->_relationship;\n";
                $result .= "\t}\n";
            }
        }

        $result .= $this->generateByteArrayGettersAndSetters($table->columns);

        $result .= "\t// others\n";
        foreach ($table->columns as $column) {
            if ($column->name === "deleted") {
                $result .= $this->generateDeleteFunction($table->type);
                break;
            }
        }
        if ($table->type === 'table') {
            $result .= $this->generateSaveFunction();
        }

        $result.="}\n\n";

        return $result;
    }

    public function generateTableQueryBase($table) {
        $namespace_prefix = $this->namespace_prefix;

        $query            = "<?php" . "\n\n";
        $query .= "namespace {$namespace_prefix}data\Base;\n";
        $query .= "\n";
        $query .= "use Repel\Framework;\n\n";
        $table_name       = BaseGenerator::singular($table->name);
        $this->table_name = $table_name;

        $query .= "class " . self::getQueryBaseName($table->name) . " extends Framework\RActiveQuery {\n\n";

        foreach ($table->columns as $column) {
            $query .= $this->generateFindByFunction($column);
        }

        foreach ($table->columns as $column) {
            $query .= $this->generateFindOneByFunction($column);
        }

        foreach ($table->columns as $column) {
            $query .= $this->generateFilterByFunction($column);
        }

        foreach ($table->columns as $column) {
            $query .= $this->generateOrderByFunction($column);
        }
        $query.= "}\n";
        return $query;
    }

    public function generateObjectProperties($columns) {
        $result = "\t// properties\n";
        foreach ($columns as $column) {
            $result.="\tpublic \${$column->name};\n";
            if ($column->foreign_key !== null) {
                $this->foreign_keys[$column->name] = $column->foreign_key;
            }
        }
        return $result;
    }

    public function generateColumnTypesArray($columns) {
        $result = "\tpublic \$TYPES = array(\n";
        foreach ($columns as $column) {
            $result .= "\t\t\"{$column->name}\" => \"{$column->type}\",\n";
        }
        $result .= "\t\t\"_repel_custom\" => \"repel\",\n";
        $result .= "\t\t\"_repel_custom_1\" => \"repel\",\n";
        $result .= "\t\t\"_repel_custom_array\" => \"repel\",\n";
        $result .= "\t);";
        return $result;
    }

    public function generateAutoIncrementArray($columns) {
        $result = "\tpublic \$AUTO_INCREMENT = array(\n";
        foreach ($columns as $column) {
            if (substr($column->default, 0, 7) === "nextval") {
                $result .= "\t\t\"{$column->name}\",\n";
            }
        }
        $result .= "\t);";
        return $result;
    }

    public function generatePrimaryKeysArray($columns) {
        $this->primary_keys = array();
        $result             = "\tpublic \$PRIMARY_KEYS = array(\n";
        foreach ($columns as $column) {
            if ($column->is_primary_key) {
                $result .= "\t\t\"{$column->name}\",\n";
                $this->primary_keys[] = $column->name;
            }
        }
        $result .= "\t);";
        return $result;
    }

    public function generateDefaultArray($columns) {
        $result = "\tpublic \$DEFAULT = array(\n";
        foreach ($columns as $column) {
            if ($column->default !== null) {
                $result .= "\t\t\"{$column->name}\",\n";
            }
        }
        $result .= "\t);";
        return $result;
    }

    public function generateForeignKeyObjects() {
        $result = "\t// foreign key objects\n";
        foreach ($this->foreign_keys as $name => $fk) {
            $object_name = mb_convert_case(BaseGenerator::singular(substr($name, 0, strlen($name) - 3), false), MB_CASE_LOWER, 'UTF-8');
            $result .= "\tprivate \$_{$object_name} = null;\n";
        }
        return $result;
    }

    public function generateByteArrayGettersAndSetters($columns) {
        $result = "\t// byte array getters and setters\n";

        $success = false;
        foreach ($columns as $column) {
            if ($column->type === "bytea") {
                $result .= "\tpublic function set" . BaseGenerator::singular($column->name) . "(\$data) {\n";
                $result .= "\t\t\$this->{$column->name} = \$data;\n";
                $result .= "\t\t\n";
                $result .= "\t}\n\n";
                $result .= "\tpublic function get" . BaseGenerator::singular($column->name) . "() {\n";
                $result .= "\t\treturn fpassthru(\$this->{$column->name});\n";
                $result .= "\t}\n";
                $success = true;
            }
        }

        if ($success) {
            $result .= "\n";
            return $result;
        } else {
            return "";
        }
    }

    public function generateRelationshipObjects($relationships) {
        $result = "\t// relationships objects\n";
        foreach ($relationships as $relationship) {
            $result .= "\tprivate \$_{$relationship->table} = null;\n";
            if ($relationship->type === 'one-to-many') {
                // nothing special
            } else if ($relationship->type === 'many-to-many') {
                $this->cross_reference = true;
            }
        }
        return $result;
    }

    public function generateForeignKeyMethods() {
        $result = "\t// foreign key methods\n";
        foreach ($this->foreign_keys as $name => $fk) {
            $class_name = BaseGenerator::singular($fk->referenced_table);
            if (substr($name, strlen($name) - 3) === "_id") {
                $key = substr($name, 0, strlen($name) - 3);
            } else {
                $key = $name;
            }
            $function_name = BaseGenerator::singular($key, true);
            $object_name   = mb_convert_case(BaseGenerator::singular($key, false), MB_CASE_LOWER, 'UTF-8');

            $result .= "\tpublic function get{$function_name}() {\n";
            $result .= "\tif(\$this->_{$object_name} === null) {\n";
            $result .= "\t\t\$this->_{$object_name} = data\D{$class_name}::finder()->findByPK(\$this->{$name});\n";
            $result .= "\t}\n";
            $result .= "\treturn \$this->_{$object_name};\n";
            $result .= "\t}\n";
        }
        return $result;
    }

    public function generateRelationshipMethods($relationships) {
        $result = "\t// relationship methods\n";
        foreach ($relationships as $relationship) {
            $function_name      = BaseGenerator::firstLettersToUpper($relationship->table);
            $active_record_name = BaseGenerator::singular($relationship->table);
            $object_name        = $relationship->table;
            if ($relationship->type === 'one-to-many') {
                $foreign_key_name = $relationship->foreign_key->referenced_column;

                $result .= "\tpublic function get{$function_name}(\$condition = null, \$parameters = null) {\n";
                $result .= "\t\tif(\$this->_{$object_name} === null) {\n";
                $result .= "\t\t\tif(!\$condition instanceof Framework\RActiveRecordCriteria) {\n";
                $result .= "\t\t\t\t\$criteria = new Framework\RActiveRecordCriteria(\$condition, \$parameters);\n";
                $result .= "\t\t\t} else {\n";
                $result .= "\t\t\t\t\$criteria = \$condition;\n";
                $result .= "\t\t\t}\n";
                $result .= "\t\t\tif(strlen(\$criteria->Condition) > 0) {\n";
                $result .= "\t\t\t\t\$criteria->Condition .= ' AND {$foreign_key_name} = :{$foreign_key_name}';\n";
                $result .= "\t\t\t} else {\n";
                $result .= "\t\t\t\t\$criteria->Condition = '{$foreign_key_name} = :{$foreign_key_name}';\n";
                $result .= "\t\t\t}\n";
                $result .= "\t\t\t\$criteria->Parameters[':{$foreign_key_name}'] = \$this->{$foreign_key_name};\n";
                $result .= "\t\t\t\$this->_{$object_name} = data\D{$active_record_name}::finder()->find(\$criteria);\n";
                $result .= "\t\t}\n";
                $result .= "\t\treturn \$this->_{$object_name};\n";
                $result .= "\t\t}\n\n";
            } else if ($relationship->type === 'many-to-many') {
                $foreign_key_name = mb_convert_case(BaseGenerator::singular($object_name), MB_CASE_LOWER, 'UTF-8') . "_id";
                $m2m_table_name   = BaseGenerator::singular($relationship->source);
                $primary_key_name = mb_convert_case(BaseGenerator::singular($this->table_name, false), MB_CASE_LOWER, 'UTF-8') . "_id";

                $relationship_table_singular  = mb_convert_case(BaseGenerator::singular($relationship->table, false), MB_CASE_LOWER, 'UTF-8');
                $relationship_source_singular = mb_convert_case(BaseGenerator::singular($relationship->source, false), MB_CASE_LOWER, 'UTF-8');

                $result .= "\tpublic function get{$function_name}(\$condition = null, \$parameters = null) {\n";
                $result .= "\t\tif(\$this->_{$object_name} === null) {\n";
                $result .= "\t\t\tif(!\$condition instanceof Framework\RActiveRecordCriteria) {\n";
                $result .= "\t\t\t\t\$criteria = new Framework\RActiveRecordCriteria(\$condition, \$parameters);\n";
                $result .= "\t\t\t} else {\n";
                $result .= "\t\t\t\t\$criteria = \$condition;\n";
                $result .= "\t\t\t}\n";
                $result .= "\t\t\tif(strlen(\$criteria->Condition) > 0) {\n";
                $result .= "\t\t\t\t\$criteria->Condition .= ' AND {$primary_key_name} = :{$primary_key_name}';\n";
                $result .= "\t\t\t} else {\n";
                $result .= "\t\t\t\t\$criteria->Condition = '{$primary_key_name} = :{$primary_key_name}';\n";
                $result .= "\t\t\t}\n";
                $result .= "\t\t\t\$criteria->Parameters[':{$primary_key_name}'] = \$this->{$primary_key_name};\n";
                $result .= "\t\t\t\${$relationship->source} = data\D{$m2m_table_name}::finder()->find(\$criteria);\n";
                $result .= "\t\t\t\${$relationship->table}_pks = array();\n";
                $result .= "\t\t\tforeach(\${$relationship->source} as \${$relationship_source_singular}) {\n";
                $result .= "\t\t\t\t\${$relationship->table}_pks[] = \${$relationship_source_singular}->{$foreign_key_name};\n";
                $result .= "\t\t\t}\n";
                $result .= "\t\t\tif(count(\${$relationship->table}_pks) > 0) {\n";
                $result .= "\t\t\t\t\$this->_{$relationship->table} = data\D{$active_record_name}::finder()->findByPKs(\${$relationship->table}_pks);\n";
                $result .= "\t\t\t\tforeach(\$this->_{$relationship->table} as \${$relationship_table_singular}) {\n";
                $result .= "\t\t\t\t\tforeach(\${$relationship->source} as \${$relationship_source_singular}) {\n";
                $result .= "\t\t\t\t\t\tif(\${$relationship_table_singular}->{$foreign_key_name} === \${$relationship_source_singular}->{$foreign_key_name}) {\n";
                $result .= "\t\t\t\t\t\t\t\${$relationship_table_singular}->setRelationship(\${$relationship_source_singular});\n";
                $result .= "\t\t\t\t\t\t\tunset(\${$relationship_source_singular});\n";
                $result .= "\t\t\t\t\t\t\tbreak;\n";
                $result .= "\t\t\t\t\t\t}\n";
                $result .= "\t\t\t\t\t}\n";
                $result .= "\t\t\t\t}\n";
                $result .= "\t\t\t} else {\n";
                $result .= "\t\t\t\t\$this->_{$object_name} = array();\n";
                $result .= "\t\t\t}\n";
                $result .= "\t\t}\n";
                $result .= "\t\treturn \$this->_{$object_name};\n";
                $result .= "\t}\n\n";
            }
        }
        return $result;
    }

    public function generateFindByFunction($column) {
        $function_name = BaseGenerator::firstLettersToUpper($column->name);

        $result = "\tpublic function findBy{$function_name}(\${$column->name}) {\n";
        $result .= "\t\treturn self::findByColumn(\"{$column->name}\", \${$column->name});\n";
        $result .= "\t}\n\n";

        return $result;
    }

    public function generateFindOneByFunction($column) {
        $function_name = BaseGenerator::firstLettersToUpper($column->name);

        $result = "\tpublic function findOneBy{$function_name}(\${$column->name}) {\n";
        $result .= "\t\treturn self::findOneByColumn(\"{$column->name}\", \${$column->name});\n";
        $result .= "\t}\n\n";
        return $result;
    }

    public function generateFilterByFunction($column) {
        $function_name = BaseGenerator::firstLettersToUpper($column->name);

        $result = "\tpublic function filterBy{$function_name}(\${$column->name}, \$operator = Framework\ROperator::EQUAL) {\n";
        $result .= "\t\treturn self::filterByColumn(\"{$column->name}\", \${$column->name}, \$operator);\n";
        $result .= "\t}\n\n";

        return $result;
    }

    public function generateOrderByFunction($column) {
        $function_name = BaseGenerator::firstLettersToUpper($column->name);

        $result = "\tpublic function orderBy{$function_name}(\$order) {\n";
        $result .= "\t\treturn self::orderByColumn(\"{$column->name}\", \$order);\n";
        $result .= "\t}\n\n";

        return $result;
    }

    public function generateDeleteFunction($type) {
        $result = "\tpublic function delete(\$physical=false) {\n";
        $result .= "\t\tif(\$physical){\n";
        $result .= "\t\treturn parent::delete();\n";
        $result .= "\t\t}\n";
        $result .= "\t\t\$this->deleted = time();\n";
        $result .= "\t\treturn \$this->save();\n";
        $result .= "\t}\n";

        return $result;
    }

    public function generateSaveFunction() {
        if (count($this->primary_keys) > 0) {
            $primary_key = $this->primary_keys[0];

            $result = "\tpublic function save(\$criteria = null) {\n";
            $result .= "\t\t\$record = parent::save(\$criteria);\n";
            $result .= "\t\tif(\$this->{$primary_key} === null) {\n";
            $result .= "\t\t\tforeach(\$this->TYPES as \$attr => \$type) {\n";
            $result .= "\t\t\t\tif (isset(\$record->\$attr)){\n";
            $result .= "\t\t\t\t\t\$this->\$attr = \$record->\$attr;\n";
            $result .= "\t\t\t\t}\n";
            $result .= "\t\t\t}\n";
            $result .= "\t\t\t\$this->_record = true;\n";
            $result .= "\t\t}\n";
            $result .= "\t\treturn \$this->{$primary_key};\n";
            $result .= "\t}\n";

            return $result;
        }
    }

}
