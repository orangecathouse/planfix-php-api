<?php

namespace OrangeCatHouse\PlanfixPhpApi;

class Planfix {

	protected $API_SERVER = 'https://api.planfix.ru/xml/';
	protected $API_KEY;
	protected $API_TOKEN;
	protected $API_ACCOUNT;
	protected $fields;
	protected $method;
	protected $parentKey;
	protected $pageCurrent = 1;

	public function __construct() {

	}

	public function setApiKey($key) {
		$this->API_KEY = $key;
		return $this;
	}

	public function setApiToken($token) {
		$this->API_TOKEN = $token;
		return $this;
	}

	public function setApiAccount($account) {
		$this->API_ACCOUNT = $account;
		return $this;
	}

	public function setMethod($method) {
		$this->method = $method;
		return $this;
	}

	public function setParentKey($parentKey) {
		$this->parentKey = $parentKey;
		return $this;
	}

	public function setPageCurrent($pageCurrent) {
		$this->pageCurrent = $pageCurrent;
		return $this;
	}

	public function setField($name, $value) {
		$this->fields[$name] = $value;
		return $this;
	}

	public function parseApi($response) {
		$result = [
			'success'       => 1,
			'error_code'    => '',
			'error_message' => '',
			'meta'          => null,
			'data'          => null
		];

		try {
			$responseXml = new \SimpleXMLElement($response);
		} catch (\Exception $e) {
			$result['success'] = 0;
			$result['error_message'] = $e->getMessage();

			return $result;
		}

		if ((string) $responseXml->attributes()->status == 'error') {
			$result['success'] = 0;
			$result['error_code'] = (string) $responseXml->code;
			$result['error_message'] = isset(Errors::$errorMap[$result['error_code']]) ? Errors::$errorMap[$result['error_code']] : 'Неизвестный код ошибки. Рекомендация: обновить.';

			return $result;
		}

		$responseXml = $responseXml->children();

		foreach($responseXml->attributes() as $key => $val) {
			$result['meta'][$key] = (int) $val;
		}

		if ($result['meta'] == null || $result['meta']['totalCount'] || $result['meta']['count']) {
			$result['data'] = $this->exportData($responseXml);
		}

		if(isset($result['data']['records'])) {
			$result['data'] = $result['data']['records']['records'];
		}

		if(isset($result['data']['record'])) {
			$result['data'][0] = $result['data']['record'];
			unset($result['data']['record']);
		}

		return $result;
	}

	protected function exportData($responseXml) {
		$items = [];

		if(!is_object($responseXml)) {
			return $items;
		}

		$child = (array) $responseXml;

		if (sizeof($child) > 1) {
			foreach($child as $key => $value) {
				if ($key == '@attributes') {
					continue;
				}
				if (is_array($value)) {
					foreach($value as $subKey => $subValue) {
						if (!is_object($subValue)) {
							$items[$key][$subKey] = $subValue;
						} else {
							if ($subValue instanceof \SimpleXMLElement) {
								$items[$key][$subKey] = $this->exportData($subValue);
							}
						}
					}
				} else {
					if (!is_object($value)) {
						$items[$key] = $value;
					} else {
						if ($value instanceof \SimpleXMLElement) {
							$items[$key] = $this->exportData($value);
						}
					}
				}
			}
		} else {
			if (sizeof($child) > 0) {
				foreach ($child as $key => $value) {
					if (!is_array($value) && !is_object($value)) {
						$items[$key] = $value;
					} else {
						if (is_object($value)) {
							$items[$key] = $this->exportData($value);
						} else {
							foreach ($value as $subKey => $subValue) {
								if (!is_object($subValue)) {
									$items[$responseXml->getName()][$subKey] = $subValue;
								} else {
									if ($subValue instanceof \SimpleXMLElement) {
										$items[$responseXml->getName()][$subKey] = $this->exportData($subValue);
									}
								}
							}
						}
					}
				}
			}
		}

		return $items;
	}

	public function send() {
		$ch = curl_init($this->API_SERVER);

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // не выводи ответ на stdout
		curl_setopt($ch, CURLOPT_HEADER, 1);   // получаем заголовки

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $this->API_KEY . ':' . $this->API_TOKEN);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->createXml()->asXML());
		//echo $requestXml->asXML();

		$response = curl_exec($ch);
		$error = curl_error($ch);
		//var_dump($error);

		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$responseBody = substr($response, $header_size);

		curl_close($ch);

		return $this->parseApi($responseBody);
	}

	protected function createXml() {
		$requestXml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><request></request>');
		if (!$this->API_ACCOUNT) {
			throw new \Exception('Account is not set');
		}
		$requestXml->account = $this->API_ACCOUNT;
		$requestXml->addAttribute('method', $this->method);
		if(isset($this->fields) AND !empty($this->fields)) {
			foreach($this->fields as $k=>$v) {
				if(is_array($v)) {
					foreach($v as $vk=>$vv) {
						$requestXml->addChild($k)->addChild($vk, $vv);
					}
				} else {
					$requestXml->addChild($k, $v);
				}
			}
		}
		$requestXml->addChild('parentKey', $this->parentKey);
		$requestXml->addChild('pageCurrent', $this->pageCurrent);
		$requestXml->addChild('pageSize', 100);

		return $requestXml;
	}

}