<?php

namespace OrangeCatHouse\PlanfixPhpApi;

class Handbook extends Planfix {

	protected $api;

	public function __construct($api) {
		$this->api = $api;
	}

	public function getGroupList() {
		return $this->api->setMethod('handbook.getGroupList')->send();
	}

	public function getList() {
		return $this->api->setMethod('handbook.getList')->send();
	}

	public function getStructure($id) {
		return $this->api->setMethod('handbook.Structure')->setField('handbook', ['id', $id])->send();
	}

	public function getRecords() {
		
	}

	public function getRecord() {
		
	}

	public function getRecordMulti() {
		
	}

	public function addRecord() {
		
	}

	public function updateRecord() {
		
	}

}
