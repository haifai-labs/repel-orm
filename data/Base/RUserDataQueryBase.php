<?php

namespace data\Base;

use Repel\Framework;

class RUserDataQueryBase extends Framework\RActiveQuery {

	public function findByAddress($address) {
		return self::findByColumn("address", $address);
	}

	public function findByCity($city) {
		return self::findByColumn("city", $city);
	}

	public function findByDateCreated($date_created) {
		return self::findByColumn("date_created", $date_created);
	}

	public function findByFirstName($first_name) {
		return self::findByColumn("first_name", $first_name);
	}

	public function findByLastName($last_name) {
		return self::findByColumn("last_name", $last_name);
	}

	public function findByUserDataId($user_data_id) {
		return self::findByColumn("user_data_id", $user_data_id);
	}

	public function findByUserId($user_id) {
		return self::findByColumn("user_id", $user_id);
	}

	public function findByZip($zip) {
		return self::findByColumn("zip", $zip);
	}

	public function findOneByAddress($address) {
		return self::findOneByColumn("address", $address);
	}

	public function findOneByCity($city) {
		return self::findOneByColumn("city", $city);
	}

	public function findOneByDateCreated($date_created) {
		return self::findOneByColumn("date_created", $date_created);
	}

	public function findOneByFirstName($first_name) {
		return self::findOneByColumn("first_name", $first_name);
	}

	public function findOneByLastName($last_name) {
		return self::findOneByColumn("last_name", $last_name);
	}

	public function findOneByUserDataId($user_data_id) {
		return self::findOneByColumn("user_data_id", $user_data_id);
	}

	public function findOneByUserId($user_id) {
		return self::findOneByColumn("user_id", $user_id);
	}

	public function findOneByZip($zip) {
		return self::findOneByColumn("zip", $zip);
	}

	public function filterByAddress($address, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("address", $address, $operator);
	}

	public function filterByCity($city, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("city", $city, $operator);
	}

	public function filterByDateCreated($date_created, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("date_created", $date_created, $operator);
	}

	public function filterByFirstName($first_name, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("first_name", $first_name, $operator);
	}

	public function filterByLastName($last_name, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("last_name", $last_name, $operator);
	}

	public function filterByUserDataId($user_data_id, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("user_data_id", $user_data_id, $operator);
	}

	public function filterByUserId($user_id, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("user_id", $user_id, $operator);
	}

	public function filterByZip($zip, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("zip", $zip, $operator);
	}

	public function orderByAddress($order) {
		return self::orderByColumn("address", $order);
	}

	public function orderByCity($order) {
		return self::orderByColumn("city", $order);
	}

	public function orderByDateCreated($order) {
		return self::orderByColumn("date_created", $order);
	}

	public function orderByFirstName($order) {
		return self::orderByColumn("first_name", $order);
	}

	public function orderByLastName($order) {
		return self::orderByColumn("last_name", $order);
	}

	public function orderByUserDataId($order) {
		return self::orderByColumn("user_data_id", $order);
	}

	public function orderByUserId($order) {
		return self::orderByColumn("user_id", $order);
	}

	public function orderByZip($order) {
		return self::orderByColumn("zip", $order);
	}

}
