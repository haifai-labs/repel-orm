<?php

namespace data\Base;

use data;
use Repel\Framework;

class RAdminBase extends Framework\RActiveRecord {

	public $_DATABASE = "primary";
	public $ADAPTER = "pgsql";
	public $TABLE = "admins";

	public $TYPES = array(
		"admin_id" => "integer",
		"company_id" => "integer",
		"deleted" => "integer",
		"email" => "text",
		"first_name" => "text",
		"last_name" => "text",
		"login" => "text",
		"password" => "text",
		"_repel_custom" => "repel",
		"_repel_custom_1" => "repel",
		"_repel_custom_array" => "repel",
	);

	public $AUTO_INCREMENT = array(
		"admin_id",
	);

	public $PRIMARY_KEYS = array(
		"admin_id",
	);

	public $DEFAULT = array(
		"admin_id",
		"deleted",
	);

	// properties
	public $admin_id;
	public $company_id;
	public $deleted;
	public $email;
	public $first_name;
	public $last_name;
	public $login;
	public $password;

	// foreign key objects
	private $_company = null;

	// relationships objects
	private $_admins_privileges = null;

	// foreign key methods
	public function getCompany() {
	if($this->_company === null) {
		$this->_company = data\DCompany::finder()->findByPK($this->company_id);
	}
	return $this->_company;
	}
	// relationship methods
	public function getAdminsPrivileges($condition = null, $parameters = null) {
		if($this->_admins_privileges === null) {
			if(!$condition instanceof Framework\RActiveRecordCriteria) {
				$criteria = new Framework\RActiveRecordCriteria($condition, $parameters);
			} else {
				$criteria = $condition;
			}
			if(strlen($criteria->Condition) > 0) {
				$criteria->Condition .= ' AND admin_id = :admin_id';
			} else {
				$criteria->Condition = 'admin_id = :admin_id';
			}
			$criteria->Parameters[':admin_id'] = $this->admin_id;
			$this->_admins_privileges = data\DAdminPrivilege::finder()->find($criteria);
		}
		return $this->_admins_privileges;
		}

	// others
	public function delete() {
		$this->deleted = time();
		return $this->save();
	}
	public function save() {
		$record = parent::save();
		if($this->admin_id === null) {
			foreach($this->TYPES as $attr => $type) {
				$this->$attr = $record->$attr;
			}
			$this->_record = true;
		}
		return $this->admin_id;
	}
}

