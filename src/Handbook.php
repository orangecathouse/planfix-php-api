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

	public function getStructure($handbook_id) {
		return $this->api->setMethod('handbook.getStructure')->setField('handbook', ['id' => $handbook_id])->send();
	}

	public function getRecords($handbook_id) {
		return $this->api->setMethod('handbook.getRecords')->setField('handbook', ['id' => $handbook_id])->send();
	}

	public function getRecord($handbook_id, $key_id) {
		return $this->api->setMethod('handbook.getRecord')
			->setField('handbook', ['id' => $handbook_id])
			->setField('key', $key_id)
			->send();
	}

	public function findIdFromName($name) {
		$list = $this->getList();
		if(isset($list['data']['handbooks'])) {
			$data = $list['data']['handbooks']['handbook'];
		} else {
			$data = $list['data']['handbook'];
		}

		$column = array_column($data, 'name');

		$found_id = array_search($name, $column);

		if(is_int($found_id)) {
			return $data[$found_id]['id'];
		} else {
			return False;
		}
	}

// 	public function WgetRecordMulti($handbook_id, $keys_id) {
// 		$this->api->setMethod('handbook.getRecordsMulti')->setField('handbook', ['id' => $handbook_id]);
// 		foreach($keys_id as $key) {
// 			$this->api->setField('records', ['key' => $key]);
// 		}
// 		$this->api->send();
// 	}
//
// 	public function WaddRecord() {
//
// 	}
//
// 	public function WupdateRecord() {
//
// 	}

}