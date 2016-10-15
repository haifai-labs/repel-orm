<?php

namespace data\Base;

use data;
use Repel\Framework;

class RPrivilegeBase extends Framework\RActiveRecord {

	public $_DATABASE = "primary";
	public $ADAPTER = "pgsql";
	public $TABLE = "privileges";

	public $TYPES = array(
		"description" => "text",
		"name" => "text",
		"privilege_id" => "integer",
		"_repel_custom" => "repel",
		"_repel_custom_1" => "repel",
		"_repel_custom_array" => "repel",
	);

	public $AUTO_INCREMENT = array(
		"privilege_id",
	);

	public $PRIMARY_KEYS = array(
		"privilege_id",
	);

	public $DEFAULT = array(
		"privilege_id",
	);

	// properties
	public $description;
	public $name;
	public $privilege_id;

	// relationships objects
	private $_admins_privileges = null;

	// relationship methods
	public function getAdminsPrivileges($condition = null, $parameters = null) {
		if($this->_admins_privileges === null) {
			if(!$condition instanceof Framework\RActiveRecordCriteria) {
				$criteria = new Framework\RActiveRecordCriteria($condition, $parameters);
			} else {
				$criteria = $condition;
			}
			if(strlen($criteria->Condition) > 0) {
				$criteria->Condition .= ' AND privilege_id = :privilege_id';
			} else {
				$criteria->Condition = 'privilege_id = :privilege_id';
			}
			$criteria->Parameters[':privilege_id'] = $this->privilege_id;
			$this->_admins_privileges = data\DAdminPrivilege::finder()->find($criteria);
		}
		return $this->_admins_privileges;
		}

	// others
	public function save() {
		$record = parent::save();
		if($this->privilege_id === null) {
			foreach($this->TYPES as $attr => $type) {
				$this->$attr = $record->$attr;
			}
			$this->_record = true;
		}
		return $this->privilege_id;
	}
}

