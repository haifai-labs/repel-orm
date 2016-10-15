<?php

namespace data\Base;

use data;
use Repel\Framework;

class RCompanyBase extends Framework\RActiveRecord {

	public $_DATABASE = "primary";
	public $ADAPTER = "pgsql";
	public $TABLE = "companies";

	public $TYPES = array(
		"company_id" => "integer",
		"deleted" => "integer",
		"full_name" => "text",
		"name" => "text",
		"_repel_custom" => "repel",
		"_repel_custom_1" => "repel",
		"_repel_custom_array" => "repel",
	);

	public $AUTO_INCREMENT = array(
		"company_id",
	);

	public $PRIMARY_KEYS = array(
		"company_id",
	);

	public $DEFAULT = array(
		"company_id",
		"deleted",
	);

	// properties
	public $company_id;
	public $deleted;
	public $full_name;
	public $name;

	// relationships objects
	private $_admins = null;

	// relationship methods
	public function getAdmins($condition = null, $parameters = null) {
		if($this->_admins === null) {
			if(!$condition instanceof Framework\RActiveRecordCriteria) {
				$criteria = new Framework\RActiveRecordCriteria($condition, $parameters);
			} else {
				$criteria = $condition;
			}
			if(strlen($criteria->Condition) > 0) {
				$criteria->Condition .= ' AND company_id = :company_id';
			} else {
				$criteria->Condition = 'company_id = :company_id';
			}
			$criteria->Parameters[':company_id'] = $this->company_id;
			$this->_admins = data\DAdmin::finder()->find($criteria);
		}
		return $this->_admins;
		}

	// others
	public function delete() {
		$this->deleted = time();
		return $this->save();
	}
	public function save() {
		$record = parent::save();
		if($this->company_id === null) {
			foreach($this->TYPES as $attr => $type) {
				$this->$attr = $record->$attr;
			}
			$this->_record = true;
		}
		return $this->company_id;
	}
}

