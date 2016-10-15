<?php

namespace data\Base;

use Repel\Framework;

class RDocumentQueryBase extends Framework\RActiveQuery {

	public function findByContent($content) {
		return self::findByColumn("content", $content);
	}

	public function findByDate($date) {
		return self::findByColumn("date", $date);
	}

	public function findByDocumentId($document_id) {
		return self::findByColumn("document_id", $document_id);
	}

	public function findByFullNumber($full_number) {
		return self::findByColumn("full_number", $full_number);
	}

	public function findByUserDataId($user_data_id) {
		return self::findByColumn("user_data_id", $user_data_id);
	}

	public function findOneByContent($content) {
		return self::findOneByColumn("content", $content);
	}

	public function findOneByDate($date) {
		return self::findOneByColumn("date", $date);
	}

	public function findOneByDocumentId($document_id) {
		return self::findOneByColumn("document_id", $document_id);
	}

	public function findOneByFullNumber($full_number) {
		return self::findOneByColumn("full_number", $full_number);
	}

	public function findOneByUserDataId($user_data_id) {
		return self::findOneByColumn("user_data_id", $user_data_id);
	}

	public function filterByContent($content, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("content", $content, $operator);
	}

	public function filterByDate($date, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("date", $date, $operator);
	}

	public function filterByDocumentId($document_id, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("document_id", $document_id, $operator);
	}

	public function filterByFullNumber($full_number, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("full_number", $full_number, $operator);
	}

	public function filterByUserDataId($user_data_id, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("user_data_id", $user_data_id, $operator);
	}

	public function orderByContent($order) {
		return self::orderByColumn("content", $order);
	}

	public function orderByDate($order) {
		return self::orderByColumn("date", $order);
	}

	public function orderByDocumentId($order) {
		return self::orderByColumn("document_id", $order);
	}

	public function orderByFullNumber($order) {
		return self::orderByColumn("full_number", $order);
	}

	public function orderByUserDataId($order) {
		return self::orderByColumn("user_data_id", $order);
	}

}
