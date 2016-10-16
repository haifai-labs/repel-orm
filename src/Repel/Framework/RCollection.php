<?php

namespace Repel\Framework;

use Repel\Exceptions;

class RCollection implements \Iterator,\Countable  {

	protected $_records = array();

	protected $position = 0;


	public function __construct() {
		$this->position = 0;
	}

	public function count(){
		return count($this->_records);
	}

	public function  rewind() {
		$this->position = 0;
	}

	public function  current() {
		return $this->_records[$this->position];
	}

	public function  key() {
		return $this->position;
	}

	public function  next() {
		++$this->position;
	}

	public function  valid() {
		return isset($this->_records[$this->position]);
	}

	public function save() {
		// if ( $this->_record ) {
		// 	return RExecutor::instance( $this )->update();
		// } else {
		// 	return RExecutor::instance( $this )->insert();
		// }
	}

	public function delete(){
	}

	public function add($record){
		if ($record instanceof RActiveRecord){
			if ($this->get($record) === null){
				$this->_records[] = $record;
				return true;
			}
			return false;
		} else if ($record === null){
			return false;
		} else {
			throw new Exceptions\InvalidTypeException("Expecting instance of RActiveRecord. " . get_class($record) . " given.");
		}
	}

	protected function getIndex($index){
		if (is_int($index)){
			return $index;
		}

		if ($index instanceof RActiveRecord){
			foreach ($this->_records as $krecord => $record) {
				if ($record === $index){
					return $krecord;
				}
			}

			return null;
		}
	}

	/**
	*
	*/
	public function remove($index){
		$removed = $this->get($index);
		if ($removed !== null){
			array_splice($this->_records,$this->getIndex($index),1);
			return $removed;
		} else {
			return null;
		}
	}

	public function get($index){
		$index = $this->getIndex($index);
		if (!array_key_exists($index,$this->_records)){
			return null;
		}
		return $this->_records[$index];
	}

}
