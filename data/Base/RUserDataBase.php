<?php

namespace data\Base;

use data;
use Repel\Framework;

class RUserDataBase extends Framework\RActiveRecord {

	public $_DATABASE = "primary";
	public $ADAPTER = "pgsql";
	public $TABLE = "user_datas";

	public $TYPES = array(
		"address" => "text",
		"city" => "text",
		"date_created" => "timestamp with time zone",
		"first_name" => "text",
		"last_name" => "text",
		"user_data_id" => "integer",
		"user_id" => "integer",
		"zip" => "text",
		"_repel_custom" => "repel",
		"_repel_custom_1" => "repel",
		"_repel_custom_array" => "repel",
	);

	public $AUTO_INCREMENT = array(
		"user_data_id",
	);

	public $PRIMARY_KEYS = array(
		"user_data_id",
	);

	public $DEFAULT = array(
		"date_created",
		"user_data_id",
	);

	// properties
	public $address;
	public $city;
	public $date_created;
	public $first_name;
	public $last_name;
	public $user_data_id;
	public $user_id;
	public $zip;

	// foreign key objects
	private $_user = null;

	// relationships objects
	private $_documents = null;

	// foreign key methods
	public function getUser() {
	if($this->_user === null) {
		$this->_user = data\DUser::finder()->findByPK($this->user_id);
	}
	return $this->_user;
	}
	// relationship methods
	public function getDocuments($condition = null, $parameters = null) {
		if($this->_documents === null) {
			if(!$condition instanceof Framework\RActiveRecordCriteria) {
				$criteria = new Framework\RActiveRecordCriteria($condition, $parameters);
			} else {
				$criteria = $condition;
			}
			if(strlen($criteria->Condition) > 0) {
				$criteria->Condition .= ' AND user_data_id = :user_data_id';
			} else {
				$criteria->Condition = 'user_data_id = :user_data_id';
			}
			$criteria->Parameters[':user_data_id'] = $this->user_data_id;
			$this->_documents = data\DDocument::finder()->find($criteria);
		}
		return $this->_documents;
		}

	// others
	public function save() {
		$record = parent::save();
		if($this->user_data_id === null) {
			foreach($this->TYPES as $attr => $type) {
				$this->$attr = $record->$attr;
			}
			$this->_record = true;
		}
		return $this->user_data_id;
	}
}

