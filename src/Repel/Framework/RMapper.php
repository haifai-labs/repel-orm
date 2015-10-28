<?php

namespace Repel\Framework;

class RMapper {

    protected $_exclude;
    protected $_include;
    protected $_record;
    protected $_fixed_map;

    public function __construct($record) {
        $this->_exclude = array();
        $this->_include = array();
        $this->_record  = $record;
    }

    public static function create($record) {
        return new self($record);
    }

    public function remove($parameter) {
        if (is_array($parameter)) {
            foreach ($parameter as $p) {
                $this->_exclude[$p] = $p;
                unset($this->_include[$p]);
            }
        } else {
            $this->_exclude[$parameter] = $parameter;
            unset($this->_include[$parameter]);
        }

        return $this;
    }

    public function add($parameter) {
        if (is_array($parameter)) {
            foreach ($parameter as $p) {
                $this->_include[$p] = $p;
                unset($this->_exclude[$p]);
            }
        } else {
            $this->_include[$parameter] = $parameter;
            unset($this->_exclude[$parameter]);
        }

        return $this;
    }

    public function map() {
        if (is_array($this->_record)) {
            if (count($this->_record) > 0) {
                $json_obj = array();
                foreach ($this->_record as $o) {
                    $json_obj[] = $this->mapObject($o);
                }
            } else {
                $json_obj = array();
            }
        } else
        if (!is_array($this->_record)) {
            if ($this->_record !== null) {
                $json_obj = $this->mapObject($this->_record);
            } else {
                $json_obj = null;
            }
        } else {
            $json_obj = null;
        }

        return $json_obj;
    }

    protected function mapObject($o) {
        $map         = $o::$MAP;
        $attributtes = $o->TYPES;

        $json = new \stdClass();
        if ($this->_fixed_map === null) {
            foreach ($attributtes as $attr => $type) {
                // znajdź wszystkie powiązane pozycje w mapie
                $instances = preg_grep('/^^(\^' . $attr . '|' . $attr . ')[*]*$/', array_keys($map));

                if (count($instances) > 0) {
                    foreach ($instances as $index => $i) {
                        if ($i[0] === "^" && !key_exists($i, $this->_include)) {
                            continue;
                        }
                        if (strpos($i, '*') > 0 && ($this->_fixed_map === null || ($this->_fixed_map !== null && !key_exists($attr, $this->_fixed_map)))) {
                            $this->_fixed_map[$attr] = $attr;
                        }
                        if (!in_array($i, $this->_exclude)) {
                            $this->_fixed_map[$i] = $map[$i];
                        }
                    }
                } else {
                    // nie ma w mapie, więc należy dodać
                    if (!in_array($attr, $this->_exclude)) {
                        $this->_fixed_map[$attr] = $attr;
                    }
                }
            }
        }

        $formatter = new RFormatter($o);
        foreach ($this->_fixed_map as $php_key => $json_key) {
            if (is_array($json_key)) {
                $result           = $formatter->format($php_key, $json_key);
                $json->$result[0] = $result[1];
            } else {
                if (!in_array($php_key, $map)) {
                    $json_key = $this->camelCase($json_key);
                } else {
                    $json_key = $map[$php_key];
                }

                $php_key = str_replace("*", "", str_replace("^", "", $php_key));
                if ($o->TYPES[$php_key] === "repel") {
                    // jeśli jest to pole repela i jest puste, to nie dodawaj
                    if ($o->$php_key !== null) {
                        $json->$json_key = $o->$php_key;
                    }
                } else {
                    $json->$json_key = $o->$php_key;
                }
            }
        }

        return $json;
    }

    public function removeAll() {
        if (is_array($this->_record)) {
            if (count($this->_record) > 0) {
                $attributtes = $this->_record[0]->TYPES;
                $obj         = $this->_record[0];
            } else {
                return $this;
            }
        } else {
            $attributtes = $this->_record->TYPES;
            $obj         = $this->_record;
        }

        if ($attributtes !== null) {
            $map = $obj::$MAP;

            if ($map === null) {
                $map = array();
            }

            $all_items = array_merge($attributtes, $map);

            foreach ($all_items as $key => $value) {
                $all_items[$key] = $key;
            }

            $this->_exclude = $all_items;
        }

        return $this;
    }

    private function camelCase($str) {
        $str = preg_replace('/[^a-z0-9]+/i', ' ', $str);
        $str = trim($str);
        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
        $str = lcfirst($str);

        return $str;
    }

}
