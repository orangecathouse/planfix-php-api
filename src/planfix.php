<?php

namespace OrangeCatHouse\PlanfixPhpApi;

class Planfix {
	protected $API_SERVER = 'https://api.planfix.ru/xml/';
	protected $API_KEY;
	protected $API_TOKEN;
	protected $API_ACCOUNT;
	protected $method;
	protected $parentKey;
	protected $pageCurrent = 1;

	public static $errorMap = [
		'0001' => 'Неверный API Key',
		'0002' => 'Приложение заблокировано',
		'0003' => 'Ошибка XML разбора. Некорректный XML',
		'0004' => 'Неизвестный аккаунт',
		'0005' => 'Ключ сессии недействителен (время жизни сессии истекло)',
		'0006' => 'Неверная подпись',
		'0007' => 'Превышен лимит использования ресурсов (ограничения, связанные с лицензиями или с количеством запросов)',
		'0008' => 'Неизвестное имя функции',
		'0009' => 'Отсутствует один из обязательных параметров функции',
		'0010' => 'Аккаунт заморожен',
		'0011' => 'На площадке аккаунта производится обновление программного обеспечения',
		'0012' => 'Отсутствует сессия, не передан параметр сессии в запрос',
		'0013' => 'Неопределенный пользователь',
		'0014' => 'Пользователь неактивен',
		'0015' => 'Недопустимое значение параметра',
		'0016' => 'В данном контексте параметр не может принимать переданное значение',
		'0017' => 'Отсутствует значение для зависящего параметра',
		'0018' => 'Функции/функционал не реализована',
		'0019' => 'Заданы конфликтующие между собой параметры',
		'0020' => 'Вызов функции запрещен',
		'0021' => 'Запрошенное количество объектов больше максимально разрешенного для данной функции',
		'0022' => 'Использование API недоступно для бесплатного аккаунта',
		'1001' => 'Неверный логин или пароль',
		'1002' => 'На выполнение данного запроса отсутствуют права (привилегии)',
		'2001' => 'Запрошенный проект не существует',
		'2002' => 'На выполнение данного запроса отсутствуют права (привилегии)',
		'2003' => 'Ошибка добавления проекта',
		'3001' => 'Указанная задача не существует',
		'3002' => 'Нет доступа к над задаче',
		'3003' => 'Проект, в рамках которого создается задача, не существует',
		'3004' => 'Проект, в рамках которого создается задача, не доступен',
		'3005' => 'Ошибка добавления задачи',
		'3006' => 'Время "Приступить к работе" не может быть больше времени "Закончить работу до"',
		'3007' => 'Неопределенная периодичность, скорее всего задано несколько узлов, которые конфликтуют друг с другом или не указан ни один',
		'3008' => 'Нет доступа к задаче',
		'3009' => 'Нет доступа на изменение данных задачи',
		'3010' => 'Данную задачу отклонить нельзя (скорее всего, она уже принята этим пользователем)',
		'3011' => 'Данную задачу принять нельзя (скорее всего, она уже принята этим пользователем)',
		'3012' => 'Пользователь, выполняющий запрос, не является исполнителем задачи',
		'3013' => 'Задача не принята (для выполнения данной функции задача должна быть принята)',
		'4001' => 'На выполнение данного запроса отсутствуют права (привилегии)',
		'4002' => 'Действие не существует',
		'4003' => 'Ошибка добавления действия',
		'4004' => 'Ошибка обновления данных',
		'4005' => 'Ошибка обновления данных',
		'4006' => 'Попытка изменить статус на недозволенный',
		'4007' => 'В данном действии запрещено менять статус',
		'4008' => 'Доступ к комментария/действию отсутствует',
		'4009' => 'Доступ к задаче отсутствует',
		'4010' => 'Указанная аналитика не существует',
		'4011' => 'Для аналитики были переданы не все поля',
		'4012' => 'Указан несуществующий параметр для аналитики',
		'4013' => 'Переданные данные не соответствуют типу поля',
		'4014' => 'Указанный ключ справочника нельзя использовать',
		'4015' => 'Указанный ключ справочника не существует',
		'4016' => 'Указанный ключ данных поля не принадлежит указанной аналитике',
		'5001' => 'Указанная группа пользователей не существует',
		'5002' => 'На выполнение данного запроса отсутствуют права (привилегии)',
		'5003' => 'Ошибка добавления',
		'6001' => 'На выполнение данного запроса отсутствуют права (привилегии)',
		'6002' => 'Данный e-mail уже используется',
		'6003' => 'Ошибка добавления сотрудника',
		'6004' => 'Пользователь не существует',
		'6005' => 'Ошибка обновления данных',
		'6006' => 'Указан идентификатор несуществующей группы пользователей',
		'7001' => 'На выполнение данного запроса отсутствуют права (привилегии)',
		'7002' => 'Клиент не существует',
		'7003' => 'Ошибка добавления клиента',
		'7004' => 'Ошибка обновления данных',
		'8001' => 'На выполнение данного запроса отсутствуют права (привилегии)',
		'8002' => 'Контакт не существует',
		'8003' => 'Ошибка добавления контакта',
		'8004' => 'Ошибка обновления данных',
		'8005' => 'Контакт не активировал доступ в ПланФикс',
		'8006' => 'Контакту не предоставлен доступ в ПланФикс',
		'8007' => 'E-mail, указанный для логина, не уникален',
		'8008' => 'Попытка установки пароля для контакта, не активировавшего доступ в ПланФикс',
		'8009' => 'Ошибка обновления данных для входа в систему',
		'9001' => 'На выполнение данного запроса отсутствуют права (привилегии)',
		'9002' => 'Запрашиваемый файл не существует',
		'9003' => 'Ошибка загрузки файла',
		'9004' => 'Попытка загрузить пустой список файлов',
		'9005' => 'Недопустимый символ в имени файла',
		'9006' => 'Имя файла не уникально',
		'9007' => 'Ошибка файловой системы',
		'9008' => 'Ошибка возникает при попытке добавить файл из проекта для проекта',
		'9009' => 'Файл, который пытаются добавить к задаче, является файлом другого проекта',
		'10001' => 'На выполнение данного запроса отсутствуют права (привилегии)',
		'10002' => 'Аналитика не существует',
		'10003' => 'Переданный параметр группы аналитики не существует',
		'10004' => 'Переданный параметр справочника аналитики не существует',
		'11001' => 'Указанной подписки не существует',
	];

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
			$result['error_message'] = static::$errorMap[$result['error_code']] ?: null;
		
			return $result;
		}
		$responseXml = $responseXml->children();
		foreach($responseXml->attributes() as $key => $val) {
			$result['meta'][$key] = (int) $val;
		}
		if ($result['meta'] == null || $result['meta']['totalCount'] || $result['meta']['count']) {
			$result['data'] = $this->exportData($responseXml);
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
		$requestXml->addChild('handbook')->addChild('id', 24908);
		$requestXml->addChild('parentKey', $this->parentKey);
		$requestXml->addChild('pageCurrent', $this->pageCurrent);
		$requestXml->addChild('pageSize', 100);
		// print_r($requestXml->asXML());
		// exit;
		return $requestXml;
	}

}