<?php

namespace Repel\Framework;

class RActiveRecord {

	public $_record				 = false;
	public $_repel_custom		 = null;
	public $_repel_custom_1		 = null;
	public $_repel_custom_array	 = null;
	public static $MAP			 = array();

	public function __construct() {
		$this->_repel_custom = null;
	}

	static public function finder() {
		$className	 = get_called_class();
		$queryClass	 = "{$className}Query";
		return new $queryClass($className);
	}

	public function save($criteria = null) {
		if ($this->_record) {
			return RExecutor::instance($this)->update();
		} else {
			return RExecutor::instance($this)->insert($criteria);
		}
	}

	public function delete() {
		if ($this->_record) {
			return RExecutor::instance($this)->delete();
		}
	}

	public function copy() {
		$class		 = get_called_class();
		$new_object	 = new $class();

		foreach ($new_object->TYPES as $name => $type) {
			if (!in_array($name, $new_object->PRIMARY_KEYS)) {
				$new_object->$name = $this->$name;
			}
		}

		return $new_object;
	}

}
