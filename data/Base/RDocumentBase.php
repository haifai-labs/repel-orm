<?php

namespace data\Base;

use data;
use Repel\Framework;

class RDocumentBase extends Framework\RActiveRecord {

	public $_DATABASE = "primary";
	public $ADAPTER = "pgsql";
	public $TABLE = "documents";

	public $TYPES = array(
		"content" => "text",
		"date" => "timestamp with time zone",
		"document_id" => "integer",
		"full_number" => "text",
		"user_data_id" => "integer",
		"_repel_custom" => "repel",
		"_repel_custom_1" => "repel",
		"_repel_custom_array" => "repel",
	);

	public $AUTO_INCREMENT = array(
		"document_id",
	);

	public $PRIMARY_KEYS = array(
		"document_id",
	);

	public $DEFAULT = array(
		"date",
		"document_id",
	);

	// properties
	public $content;
	public $date;
	public $document_id;
	public $full_number;
	public $user_data_id;

	// foreign key objects
	private $_user_data = null;

	// foreign key methods
	public function getUserData() {
	if($this->_user_data === null) {
		$this->_user_data = data\DUserData::finder()->findByPK($this->user_data_id);
	}
	return $this->_user_data;
	}
	// others
	public function save() {
		$record = parent::save();
		if($this->document_id === null) {
			foreach($this->TYPES as $attr => $type) {
				$this->$attr = $record->$attr;
			}
			$this->_record = true;
		}
		return $this->document_id;
	}
}

