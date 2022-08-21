<?php

namespace OrangeCatHouse\PlanfixPhpApi;

class Handbook extends Planfix {

	protected $api;
	protected $items = [];
	protected $groups = [];

	public function __construct($api) {
		$this->api = $api;
	}

	public function getGroups() {
		$result = $this->groups;
		$this->groups = [];
		return $result;
	}

	public function getGroupList() {
		return $this->api->setMethod('handbook.getGroupList')->send();
	}

	public function getList() {
		return $this->api->setMethod('handbook.getList')->send();
	}

	public function getStructure($handbook_id) {
		$items = [];
		$request = $this->api->setMethod('handbook.getStructure')->setField('handbook', ['id' => $handbook_id])->send();
		$data = $request['data']['handbook'];
		if(isset($data['fields']['field'])) {
				$items[$data['fields']['field']['num']] = $data['fields']['field'];
		} elseif(isset($data['fields']['fields'])) {
				foreach ($data['fields']['fields'] as $field) {
					$items[$field['num']] = $field;
				}
		}
		return $items;
	}

	public function getRecords($handbook_id, $key = NULL) {
		if($key) {
			$this->api->setParentKey($key);
		}
		$request = $this->api->setMethod('handbook.getRecords')->setField('handbook', ['id' => $handbook_id])->send();
		$data = $request['data'];
		if(isset($data['records']['record'])) {
				$this->checkRecord($handbook_id, $data['records']['record']);
		} elseif(isset($data['records']['records'])) {
				foreach ($data['records']['records'] as $new_record) {
					$this->checkRecord($handbook_id, $new_record);
				}
		}
		$result = $this->items;
		$this->items = [];
		return $result;
	}

	public function checkRecord($handbook_id, $record) {
		if($record['isGroup']) {
			$key = $record['key'];
			$this->groups[$key] = $record;
			$this->getRecords($handbook_id, $key);
		} else {
			$this->items[$record['key']] = $record;
		}
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

	// public function checkRecord($pf, $record) {
	//     if($record['isGroup']) {
	//         $key = $record['key'];
	//         getRecords($pf, $key);
	//     } else {
	//         $GLOBALS['records'][$record['key']] = $record;
	//     }
	// }
	//
	// public function getRecords($key = NULL) {
	//     if($key) {
	//         $pf->setParentKey($key);
	//     }
	//     $request = $this->pf->send();
	//     $new_records = $request['data'];
	//     if(isset($new_records['records']['record'])) {
	//         checkRecord($pf, $new_records['records']['record']);
	//     } elseif(isset($new_records['records']['records'])) {
	//         foreach ($new_records['records']['records'] as $new_record) {
	//             checkRecord($pf, $new_record);
	//         }
	//     }
	// }
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