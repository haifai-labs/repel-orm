<?php

namespace data\Base;

use data;
use Repel\Framework;

class RUserBase extends Framework\RActiveRecord {

	public $_DATABASE = "primary";
	public $ADAPTER = "pgsql";
	public $TABLE = "users";

	public $TYPES = array(
		"date_created" => "timestamp with time zone",
		"deleted" => "integer",
		"login" => "text",
		"password" => "text",
		"user_id" => "integer",
		"_repel_custom" => "repel",
		"_repel_custom_1" => "repel",
		"_repel_custom_array" => "repel",
	);

	public $AUTO_INCREMENT = array(
		"user_id",
	);

	public $PRIMARY_KEYS = array(
		"user_id",
	);

	public $DEFAULT = array(
		"date_created",
		"deleted",
		"user_id",
	);

	// properties
	public $date_created;
	public $deleted;
	public $login;
	public $password;
	public $user_id;

	// relationships objects
	private $_user_datas = null;

	// relationship methods
	public function getUserDatas($condition = null, $parameters = null) {
		if($this->_user_datas === null) {
			if(!$condition instanceof Framework\RActiveRecordCriteria) {
				$criteria = new Framework\RActiveRecordCriteria($condition, $parameters);
			} else {
				$criteria = $condition;
			}
			if(strlen($criteria->Condition) > 0) {
				$criteria->Condition .= ' AND user_id = :user_id';
			} else {
				$criteria->Condition = 'user_id = :user_id';
			}
			$criteria->Parameters[':user_id'] = $this->user_id;
			$this->_user_datas = data\DUserData::finder()->find($criteria);
		}
		return $this->_user_datas;
		}

	// others
	public function delete() {
		$this->deleted = time();
		return $this->save();
	}
	public function save() {
		$record = parent::save();
		if($this->user_id === null) {
			foreach($this->TYPES as $attr => $type) {
				$this->$attr = $record->$attr;
			}
			$this->_record = true;
		}
		return $this->user_id;
	}
}

