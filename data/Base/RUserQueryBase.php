<?php

namespace data\Base;

use Repel\Framework;

class RUserQueryBase extends Framework\RActiveQuery {

	public function findByDateCreated($date_created) {
		return self::findByColumn("date_created", $date_created);
	}

	public function findByDeleted($deleted) {
		return self::findByColumn("deleted", $deleted);
	}

	public function findByLogin($login) {
		return self::findByColumn("login", $login);
	}

	public function findByPassword($password) {
		return self::findByColumn("password", $password);
	}

	public function findByUserId($user_id) {
		return self::findByColumn("user_id", $user_id);
	}

	public function findOneByDateCreated($date_created) {
		return self::findOneByColumn("date_created", $date_created);
	}

	public function findOneByDeleted($deleted) {
		return self::findOneByColumn("deleted", $deleted);
	}

	public function findOneByLogin($login) {
		return self::findOneByColumn("login", $login);
	}

	public function findOneByPassword($password) {
		return self::findOneByColumn("password", $password);
	}

	public function findOneByUserId($user_id) {
		return self::findOneByColumn("user_id", $user_id);
	}

	public function filterByDateCreated($date_created, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("date_created", $date_created, $operator);
	}

	public function filterByDeleted($deleted, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("deleted", $deleted, $operator);
	}

	public function filterByLogin($login, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("login", $login, $operator);
	}

	public function filterByPassword($password, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("password", $password, $operator);
	}

	public function filterByUserId($user_id, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("user_id", $user_id, $operator);
	}

	public function orderByDateCreated($order) {
		return self::orderByColumn("date_created", $order);
	}

	public function orderByDeleted($order) {
		return self::orderByColumn("deleted", $order);
	}

	public function orderByLogin($order) {
		return self::orderByColumn("login", $order);
	}

	public function orderByPassword($order) {
		return self::orderByColumn("password", $order);
	}

	public function orderByUserId($order) {
		return self::orderByColumn("user_id", $order);
	}

}
