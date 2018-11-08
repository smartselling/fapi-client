<?php

if (!\class_exists('FAPIClient')) {
	class FAPIClient_Pest
	{

		/** @var mixed[] */
		public $curlOpts = [
			\CURLOPT_RETURNTRANSFER => true,
			\CURLOPT_SSL_VERIFYPEER => false,
			\CURLOPT_FOLLOWLOCATION => false,
			\CURLOPT_MAXREDIRS => 10,
		];

		/** @var string */
		public $baseUrl;

		/** @var mixed[] */
		public $lastResponse;

		/** @var mixed */
		public $lastRequest;

		/** @var mixed[] */
		public $lastHeaders;

		/** @var bool */
		public $throwExceptions = true;

		/**
		 * @param string $baseUrl
		 * @throws FAPIClient_Exception
		 */
		public function __construct($baseUrl)
		{
			if (!\function_exists('curl_init')) {
				throw new FAPIClient_Exception('CURL module not available! Pest requires CURL. See http://php.net/manual/en/book.curl.php');
			}

			if (\ini_get('open_basedir') === '' && \strtolower(\ini_get('safe_mode')) === 'off') {
				$this->curlOpts['CURLOPT_FOLLOWLOCATION'] = true;
			}

			$this->baseUrl = $baseUrl;
			$this->curlOpts[\CURLOPT_HEADERFUNCTION] = function ($ch, $str) {
				if (\preg_match('/([^:]+):\s(.+)/m', $str, $match)) {
					$this->lastHeaders[\strtolower($match[1])] = \trim($match[2]);
				}

				return \strlen($str);
			};
		}

		/**
		 * @param string $user
		 * @param string $pass
		 * @param string $auth
		 */
		public function setupAuth($user, $pass, $auth = 'basic')
		{
			$this->curlOpts[\CURLOPT_HTTPAUTH] = \constant('CURLAUTH_' . \strtoupper($auth));
			$this->curlOpts[\CURLOPT_USERPWD] = $user . ':' . $pass;
		}

		/**
		 * @param string $host
		 * @param string $port
		 * @param string|null $user
		 * @param string|null $pass
		 */
		public function setupProxy($host, $port, $user = null, $pass = null)
		{
			$this->curlOpts[\CURLOPT_PROXYTYPE] = 'HTTP';
			$this->curlOpts[\CURLOPT_PROXY] = $host;
			$this->curlOpts[\CURLOPT_PROXYPORT] = $port;

			if ($user && $pass) {
				$this->curlOpts[\CURLOPT_PROXYUSERPWD] = $user . ':' . $pass;
			}
		}

		/**
		 * @param string $url
		 * @return mixed
		 */
		public function get($url)
		{
			$curl = $this->prepRequest($this->curlOpts, $url);
			$body = $this->doRequest($curl);
			$body = $this->processBody($body);

			return $body;
		}

		/**
		 * @param string $url
		 * @param mixed[]|string $data
		 * @param mixed[] $headers
		 * @return mixed
		 */
		public function post($url, $data, $headers = [])
		{
			$data = \is_array($data)
				? \http_build_query($data, '', '&')
				: $data;

			$curl_opts = $this->curlOpts;
			$curl_opts[\CURLOPT_CUSTOMREQUEST] = 'POST';
			$headers[] = 'Content-Length: ' . \strlen($data);
			$curl_opts[\CURLOPT_HTTPHEADER] = $headers;
			$curl_opts[\CURLOPT_POSTFIELDS] = $data;
			$curl = $this->prepRequest($curl_opts, $url);
			$body = $this->doRequest($curl);
			$body = $this->processBody($body);

			return $body;
		}

		/**
		 * @param string $url
		 * @param mixed[]|string $data
		 * @param mixed[] $headers
		 * @return mixed
		 */
		public function put($url, $data, $headers = [])
		{
			$data = \is_array($data)
				? \http_build_query($data, '', '&')
				: $data;

			$curl_opts = $this->curlOpts;
			$curl_opts[\CURLOPT_CUSTOMREQUEST] = 'PUT';
			$headers[] = 'Content-Length: ' . \strlen($data);
			$curl_opts[\CURLOPT_HTTPHEADER] = $headers;
			$curl_opts[\CURLOPT_POSTFIELDS] = $data;
			$curl = $this->prepRequest($curl_opts, $url);
			$body = $this->doRequest($curl);
			$body = $this->processBody($body);

			return $body;
		}

		/**
		 * @param string $url
		 * @param mixed[]|string $data
		 * @param mixed[] $headers
		 * @return mixed
		 */
		public function patch($url, $data, $headers = [])
		{
			$data = \is_array($data)
				? \http_build_query($data, '', '&')
				: $data;

			$curl_opts = $this->curlOpts;
			$curl_opts[\CURLOPT_CUSTOMREQUEST] = 'PATCH';
			$headers[] = 'Content-Length: ' . \strlen($data);
			$curl_opts[\CURLOPT_HTTPHEADER] = $headers;
			$curl_opts[\CURLOPT_POSTFIELDS] = $data;
			$curl = $this->prepRequest($curl_opts, $url);
			$body = $this->doRequest($curl);
			$body = $this->processBody($body);

			return $body;
		}

		/**
		 * @param string $url
		 * @return mixed
		 */
		public function delete($url)
		{
			$curl_opts = $this->curlOpts;
			$curl_opts[\CURLOPT_CUSTOMREQUEST] = 'DELETE';
			$curl = $this->prepRequest($curl_opts, $url);
			$body = $this->doRequest($curl);
			$body = $this->processBody($body);

			return $body;
		}

		/**
		 * @return mixed
		 */
		public function lastBody()
		{
			return $this->lastResponse['body'];
		}

		/**
		 * @return int
		 */
		public function lastStatus()
		{
			return $this->lastResponse['meta']['http_code'];
		}

		/**
		 * @param mixed $header
		 * @return mixed
		 */
		public function lastHeader($header)
		{
			if (empty($this->lastHeaders[\strtolower($header)])) {
				return null;
			}

			return $this->lastHeaders[\strtolower($header)];
		}

		/**
		 * @param string $body
		 * @return string
		 */
		protected function processBody($body)
		{
			return $body;
		}

		/**
		 * @param string $body
		 * @return string
		 */
		protected function processError($body)
		{
			return $body;
		}

		/**
		 * @param mixed[] $opts
		 * @param string $url
		 * @return resource
		 */
		protected function prepRequest($opts, $url)
		{
			if (\strncmp($url, $this->baseUrl, \strlen($this->baseUrl)) !== 0) {
				$url = $this->baseUrl . $url;
			}

			$curl = \curl_init($url);

			foreach ($opts as $opt => $val) {
				\curl_setopt($curl, $opt, $val);
			}

			$this->lastRequest = ['url' => $url];

			if (isset($opts[\CURLOPT_CUSTOMREQUEST])) {
				$this->lastRequest['method'] = $opts[\CURLOPT_CUSTOMREQUEST];

			} else {
				$this->lastRequest['method'] = 'GET';
			}

			if (isset($opts[\CURLOPT_POSTFIELDS])) {
				$this->lastRequest['data'] = $opts[\CURLOPT_POSTFIELDS];
			}

			return $curl;
		}

		/**
		 * @param resource $curl
		 * @return mixed
		 */
		private function doRequest($curl)
		{
			$this->lastHeaders = [];
			$body = \curl_exec($curl);
			$meta = \curl_getinfo($curl);
			$this->lastResponse = ['body' => $body, 'meta' => $meta];
			\curl_close($curl);
			$this->checkLastResponseForError();

			return $body;
		}

		protected function checkLastResponseForError()
		{
			if (!$this->throwExceptions) {
				return;
			}

			$meta = $this->lastResponse['meta'];
			$body = $this->lastResponse['body'];

			if (!$meta) {
				return;
			}

			switch ($meta['http_code']) {
				case 400:
					throw new FAPIClient_Pest_BadRequest($this->processError($body));

					break;
				case 401:
					throw new FAPIClient_Pest_Unauthorized($this->processError($body));

					break;
				case 403:
					throw new FAPIClient_Pest_Forbidden($this->processError($body));

					break;
				case 404:
					throw new FAPIClient_Pest_NotFound($this->processError($body));

					break;
				case 405:
					throw new FAPIClient_Pest_MethodNotAllowed($this->processError($body));

					break;
				case 409:
					throw new FAPIClient_Pest_Conflict($this->processError($body));

					break;
				case 410:
					throw new FAPIClient_Pest_Gone($this->processError($body));

					break;
				case 422:
					throw new FAPIClient_Pest_InvalidRecord($this->processError($body));

					break;
				default:
					if ($meta['http_code'] >= 400 && $meta['http_code'] <= 499) {
						throw new FAPIClient_Pest_ClientError($this->processError($body));
					}

					if ($meta['http_code'] >= 500 && $meta['http_code'] <= 599) {
						throw new FAPIClient_Pest_ServerError($this->processError($body));
					}

					if (!$meta['http_code'] || $meta['http_code'] >= 600) {
						throw new FAPIClient_Pest_UnknownResponse($this->processError($body));
					}
			}
		}

	}

	class FAPIClient_PestJSON extends FAPIClient_Pest
	{

		/**
		 * @inheritdoc
		 */
		public function post($url, $data, $headers = [])
		{
			return parent::post($url, \json_encode($data), $headers);
		}

		/**
		 * @inheritdoc
		 */
		public function put($url, $data, $headers = [])
		{
			return parent::put($url, \json_encode($data), $headers);
		}

		/**
		 * @inheritdoc
		 */
		protected function prepRequest($opts, $url)
		{
			$opts[\CURLOPT_HTTPHEADER][] = 'Accept: application/json';
			$opts[\CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';

			return parent::prepRequest($opts, $url);
		}

		/**
		 * @param string $body
		 * @return mixed[]
		 */
		public function processBody($body)
		{
			return \json_decode($body, true);
		}

	}

	class FAPIClient
	{

		/** @var FAPIClient_ClientResource */
		public $client;

		/** @var FAPIClient_PestJSON */
		public $RESTClient;

		/** @var FAPIClient_InvoiceResource */
		public $invoice;

		/** @var FAPIClient_ItemResource */
		public $item;

		/** @var FAPIClient_PeriodicInvoiceResource */
		public $periodicInvoice;

		/** @var FAPIClient_CurrencyResource */
		public $currency;

		/** @var FAPIClient_PaymentTypeResource */
		public $paymentType;

		/** @var FAPIClient_CountryResource */
		public $country;

		/** @var FAPIClient_SettingsResource */
		public $settings;

		/** @var FAPIClient_EmailResource */
		public $email;

		/** @var FAPIClient_LogResource */
		public $log;

		/** @var FAPIClient_UserResource */
		public $user;

		/** @var FAPIClient_PaymentResource */
		public $payment;

		/** @var FAPIClient_ValidatorResource */
		public $validator;

		/** @var FAPIClient_FormResource */
		public $form;

		/** @var FAPIClient_OrderResource */
		public $order;

		/** @var FAPIClient_ItemTemplateResource */
		public $itemTemplate;

		/** @var FAPIClient_StatisticsResource */
		public $statistics;

		/** @var FAPIClient_ExchangeRateSettingResource */
		public $exchangeRateSetting;

		/** @var FAPIClient_ForeignVatSettingResource */
		public $foreignVatSetting;

		/** @var int */
		private $code;

		/**
		 * @param string $username
		 * @param string $apiToken
		 * @param string $url
		 * @throws FAPIClient_Exception
		 */
		public function __construct($username, $apiToken, $url = 'https://api.fapi.cz')
		{
			$this->RESTClient = new FAPIClient_PestJSON($url);
			$this->RESTClient->setupAuth($username, $apiToken);

			$this->client = new FAPIClient_ClientResource($this->RESTClient, $this);
			$this->invoice = new FAPIClient_InvoiceResource($this->RESTClient, $this);
			$this->item = new FAPIClient_ItemResource($this->RESTClient, $this);
			$this->periodicInvoice = new FAPIClient_PeriodicInvoiceResource($this->RESTClient, $this);
			$this->currency = new FAPIClient_CurrencyResource($this->RESTClient, $this);
			$this->paymentType = new FAPIClient_PaymentTypeResource($this->RESTClient, $this);
			$this->country = new FAPIClient_CountryResource($this->RESTClient, $this);
			$this->settings = new FAPIClient_SettingsResource($this->RESTClient, $this);
			$this->email = new FAPIClient_EmailResource($this->RESTClient, $this);
			$this->log = new FAPIClient_LogResource($this->RESTClient, $this);
			$this->user = new FAPIClient_UserResource($this->RESTClient, $this);
			$this->payment = new FAPIClient_PaymentResource($this->RESTClient, $this);
			$this->validator = new FAPIClient_ValidatorResource($this->RESTClient, $this);
			$this->form = new FAPIClient_FormResource($this->RESTClient, $this);
			$this->order = new FAPIClient_OrderResource($this->RESTClient, $this);
			$this->itemTemplate = new FAPIClient_ItemTemplateResource($this->RESTClient, $this);
			$this->statistics = new FAPIClient_StatisticsResource($this->RESTClient, $this);
			$this->exchangeRateSetting = new FAPIClient_ExchangeRateSettingResource($this->RESTClient, $this);
			$this->foreignVatSetting = new FAPIClient_ForeignVatSettingResource($this->RESTClient, $this);
		}

		public function checkConnection()
		{
			try {
				$this->RESTClient->get('');
				$this->setCode($this->RESTClient->lastResponse['meta']['http_code']);

				if ($this->RESTClient->lastResponse['body'] !== '{}') {
					throw new RuntimeException('Cannot establish a connection to the server.');
				}
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->processException($exception);
			}
		}

		public function processException(FAPIClient_Pest_Exception $exception)
		{
			$json = \json_decode($exception->getMessage());
			$class = \str_replace('Pest_', '', \get_class($exception)) . 'Exception';
			$exception = new $class(isset($json->message) ? $json->message : null, 0, $exception);

			throw $exception;
		}

		/**
		 * @return int
		 */
		public function getCode()
		{
			return $this->code;
		}

		/**
		 * @param int $code
		 */
		public function setCode($code)
		{
			$this->code = $code;
		}

	}

	abstract class FAPIClient_Resource
	{

		/** @var FAPIClient_PestJSON */
		protected $client;

		/** @var FAPIClient */
		protected $parent;

		/** @var string */
		protected $url = '';

		public function __construct(FAPIClient_PestJSON $client, FAPIClient $parent)
		{
			$this->client = $client;
			$this->parent = $parent;
		}

		/**
		 * @return mixed[][]
		 */
		public function getAll()
		{
			try {
				$response = $this->client->get($this->url);
				$this->setCode();

				return $response[\str_replace(['-', '/'], ['_', ''], $this->url)];
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

		/**
		 * @param int $id
		 * @return mixed[]
		 */
		public function get($id)
		{
			try {
				$response = $this->client->get($this->url . '/' . $id);
				$this->setCode();

				return $response;
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

		/**
		 * @param mixed[] $data
		 * @return mixed[]
		 */
		public function create($data)
		{
			try {
				$response = $this->client->post($this->url, $data);
				$this->setCode();

				return $response;
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

		/**
		 * @param int $id
		 * @param mixed[] $data
		 * @return mixed[]
		 */
		public function update($id, $data)
		{
			try {
				$response = $this->client->put($this->url . '/' . $id, $data);
				$this->setCode();

				return $response;
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

		/**
		 * @param int $id
		 * @return null
		 */
		public function delete($id)
		{
			try {
				$this->client->delete($this->url . '/' . $id);
				$this->setCode();

				return null;
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

		/**
		 * @param mixed[] $conditions
		 * @return mixed[][]
		 */
		public function search($conditions)
		{
			try {
				$response = $this->client->get($this->url . '/search?' . \http_build_query($conditions, '', '&'));
				$this->setCode();

				return $response;
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

		protected function setCode()
		{
			$this->parent->setCode($this->client->lastResponse['meta']['http_code']);
		}

	}

	class FAPIClient_ClientResource extends FAPIClient_Resource
	{

		public function __construct(FAPIClient_PestJSON $client, FAPIClient $parent)
		{
			parent::__construct($client, $parent);
			$this->url = '/clients';
		}

		/**
		 * @param int|null $limit
		 * @param int|null $offset
		 * @param string|null $order
		 * @param string|null $search
		 * @param bool|null $showStatistics
		 * @param int|null $project
		 * @param bool|null $showOnlyNames
		 * @return mixed[]
		 */
		public function getAll(
			$limit = null,
			$offset = null,
			$order = null,
			$search = null,
			$showStatistics = null,
			$project = null,
			$showOnlyNames = null
		) {
			try {
				$parameters = [
					'limit' => $limit,
					'offset' => $offset,
					'order' => $order,
					'search' => $search,
					'show_statistics' => $showStatistics,
					'project' => $project,
					'show_only_names' => $showOnlyNames,
				];

				$url = $this->url . '?' . \http_build_query($parameters, '', '&');
				$response = $this->client->get($url);
				$this->setCode();

				return $response['clients'];
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

	}

	class FAPIClient_CountryResource extends FAPIClient_Resource
	{

		public function __construct(FAPIClient_PestJSON $client, FAPIClient $parent)
		{
			parent::__construct($client, $parent);
			$this->url = '/countries';
		}

		public function get($id)
		{
			throw new FAPIClient_InvalidActionException;
		}

		public function create($data)
		{
			throw new FAPIClient_InvalidActionException;
		}

		public function update($id, $data)
		{
			throw new FAPIClient_InvalidActionException;
		}

		public function delete($id)
		{
			throw new FAPIClient_InvalidActionException;
		}

		public function search($conditions)
		{
			throw new FAPIClient_InvalidActionException;
		}

	}

	class FAPIClient_CurrencyResource extends FAPIClient_Resource
	{

		public function __construct(FAPIClient_PestJSON $client, FAPIClient $parent)
		{
			parent::__construct($client, $parent);
			$this->url = '/currencies';
		}

		public function get($id)
		{
			throw new FAPIClient_InvalidActionException;
		}

		public function create($data)
		{
			throw new FAPIClient_InvalidActionException;
		}

		public function update($id, $data)
		{
			throw new FAPIClient_InvalidActionException;
		}

		public function delete($id)
		{
			throw new FAPIClient_InvalidActionException;
		}

	}

	class FAPIClient_EmailResource extends FAPIClient_Resource
	{

		public function __construct(FAPIClient_PestJSON $client, FAPIClient $parent)
		{
			parent::__construct($client, $parent);
			$this->url = '/emails';
		}

		/**
		 * @param mixed[] $emails
		 * @return mixed[]
		 */
		public function synchronize($emails)
		{
			try {
				$response = $this->client->put($this->url . '/synchronize', ['emails' => $emails]);
				$this->setCode();

				return $response['emails'];
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

		public function get($id)
		{
			throw new FAPIClient_InvalidActionException;
		}

		public function update($id, $emails)
		{
			throw new FAPIClient_InvalidActionException;
		}

		public function delete($id)
		{
			throw new FAPIClient_InvalidActionException;
		}

	}

	class FAPIClient_InvoiceResource extends FAPIClient_Resource
	{

		public function __construct(FAPIClient_PestJSON $client, FAPIClient $parent)
		{
			parent::__construct($client, $parent);
			$this->url = '/invoices';
		}

		/**
		 * @param int
		 * @return string
		 */
		public function getPdf($id)
		{
			try {
				$this->client->get($this->url . '/' . $id . '.pdf');
				$this->setCode();

				return $this->client->lastResponse['body'];
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

		/**
		 * @param integer $limit
		 * @param integer $offset
		 * @param string $order
		 * @param string $searchKeyword
		 * @param integer $user
		 * @param string $type
		 * @param string $status
		 * @param string|array|null $createDate
		 * @param string $dateFrom
		 * @param string $dateTo
		 * @param string $lastModifiedAfter
		 * @param int|null $project
		 * @param bool|null $afterPayday
		 * @param string|array|null $paydayDate
		 * @param string|array|null $paidOn
		 * @param string|null $series
		 * @param string|null $itemNameOrDescription
		 * @param int|null $parent
		 * @return mixed[][]
		 */
		public function getAll(
			$limit = null,
			$offset = null,
			$order = null,
			$searchKeyword = null,
			$user = null,
			$type = null,
			$status = null,
			$createDate = null,
			$dateFrom = null,
			$dateTo = null,
			$lastModifiedAfter = null,
			$project = null,
			$afterPayday = null,
			$paydayDate = null,
			$paidOn = null,
			$series = null,
			$itemNameOrDescription = null,
			$parent = null
		) {
			try {
				$url = $this->url;

				if (isset($limit) || isset($offset) || isset($order) || isset($searchKeyword) || isset($user) || isset($type) || isset($status) || isset($createDate) || isset($dateFrom) || isset($dateTo) || isset($lastModifiedAfter) || isset($project) || isset($afterPayday) || isset($paydayDate) || isset($paidOn) || isset($series) || isset($itemNameOrDescription) || isset($parent)) {
					$parameters = [
						'limit' => $limit,
						'offset' => $offset,
						'order' => $order,
						'search' => $searchKeyword,
						'user' => $user,
						'type' => $type,
						'status' => $status,
						'create_date' => $createDate,
						'date_from' => $dateFrom,
						'date_to' => $dateTo,
						'last_modified_after' => $lastModifiedAfter,
						'project' => $project,
						'after_payday' => $afterPayday,
						'payday_date' => $paydayDate,
						'paid_on' => $paidOn,
						'series' => $series,
						'item_name_or_description' => $itemNameOrDescription,
						'parent' => $parent,
					];

					$url .= \sprintf('?%s', \http_build_query($parameters, '', '&'));
				}

				$response = $this->client->get($url);
				$this->setCode();

				return $response['invoices'];
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

		/**
		 * @param string $searchKeyword
		 * @param integer $user
		 * @param string $type
		 * @param string $status
		 * @param string $createDate
		 * @param string $dateFrom
		 * @param string $dateTo
		 * @param int|null $project
		 * @param bool|null $afterPayday
		 * @param int|null $form
		 * @return integer
		 */
		public function count(
			$searchKeyword = null,
			$user = null,
			$type = null,
			$status = null,
			$createDate = null,
			$dateFrom = null,
			$dateTo = null,
			$project = null,
			$afterPayday = null,
			$form = null
		) {
			try {
				$url = \sprintf('%s/count', $this->url);

				if (isset($searchKeyword) || isset($user) || isset($type) || isset($status) || isset($createDate) || isset($dateFrom) || isset($dateTo) || isset($project) || isset($afterPayday) || isset($form)) {
					$parameters = [
						'search' => $searchKeyword,
						'user' => $user,
						'type' => $type,
						'status' => $status,
						'create_date' => $createDate,
						'date_from' => $dateFrom,
						'date_to' => $dateTo,
						'project' => $project,
						'after_payday' => $afterPayday,
						'form' => $form,
					];

					$url .= \sprintf('?%s', \http_build_query($parameters, '', '&'));
				}

				$response = $this->client->get($url);
				$this->setCode();

				return $response['count'];
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

		/**
		 * @param int $id
		 * @param int $messageTemplate
		 * @return void
		 */
		public function sendEmail($id, $messageTemplate)
		{
			try {
				$this->client->post($this->url . '/send-email', [
					'invoice' => $id,
					'message_template' => $messageTemplate,
				]);

				$this->setCode();

			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}
	}

	class FAPIClient_ItemResource extends FAPIClient_Resource
	{

		public function __construct(FAPIClient_PestJSON $client, FAPIClient $parent)
		{
			parent::__construct($client, $parent);
			$this->url = '/items';
		}

		public function getAll()
		{
			throw new FAPIClient_InvalidActionException;
		}

		public function search($conditions)
		{
			throw new FAPIClient_InvalidActionException;
		}

	}

	class FAPIClient_LogResource extends FAPIClient_Resource
	{

		public function __construct(FAPIClient_PestJSON $client, FAPIClient $parent)
		{
			parent::__construct($client, $parent);
			$this->url = '/logs';
		}

		public function get($id)
		{
			throw new FAPIClient_InvalidActionException;
		}

		public function create($data)
		{
			throw new FAPIClient_InvalidActionException;
		}

		public function update($id, $data)
		{
			throw new FAPIClient_InvalidActionException;
		}

		public function delete($id)
		{
			throw new FAPIClient_InvalidActionException;
		}

		public function search($conditions)
		{
			throw new FAPIClient_InvalidActionException;
		}

	}

	class FAPIClient_PaymentTypeResource extends FAPIClient_Resource
	{

		public function __construct(FAPIClient_PestJSON $client, FAPIClient $parent)
		{
			parent::__construct($client, $parent);
			$this->url = '/payment-types';
		}

		public function get($id)
		{
			throw new FAPIClient_InvalidActionException;
		}

		public function create($data)
		{
			throw new FAPIClient_InvalidActionException;
		}

		public function update($id, $data)
		{
			throw new FAPIClient_InvalidActionException;
		}

		public function delete($id)
		{
			throw new FAPIClient_InvalidActionException;
		}

		public function search($conditions)
		{
			throw new FAPIClient_InvalidActionException;
		}

	}

	class FAPIClient_PeriodicInvoiceResource extends FAPIClient_Resource
	{

		public function __construct(FAPIClient_PestJSON $client, FAPIClient $parent)
		{
			parent::__construct($client, $parent);
			$this->url = '/periodic-invoices';
		}

		/**
		 * @param integer $limit
		 * @param integer $offset
		 * @param string $order
		 * @param string $search
		 * @param string $status
		 * @param bool $detailed
		 * @return mixed[]
		 */
		public function getAll($limit = null, $offset = null, $order = null, $search = null, $status = null, $detailed = null)
		{
			try {
				$url = $this->url;

				if (isset($limit) || isset($offset) || isset($order) || isset($search) || isset($status) || isset($detailed)) {
					$parameters = [
						'limit' => $limit,
						'offset' => $offset,
						'order' => $order,
						'search' => $search,
						'status' => $status,
						'detailed' => $detailed,
					];

					$url .= '?' . \http_build_query($parameters, '', '&');
				}

				$response = $this->client->get($url);
				$this->setCode();

				return $response['periodic_invoices'];
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

		/**
		 * @param string $search
		 * @param string $status
		 * @return int
		 */
		public function count($search = null, $status = null)
		{
			try {
				$url = $this->url . '/count';

				if (isset($search) || isset($status)) {
					$parameters = [
						'search' => $search,
						'status' => $status,
					];

					$url .= '?' . \http_build_query($parameters, '', '&');
				}

				$response = $this->client->get($url);
				$this->setCode();

				return $response['count'];
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

	}

	class FAPIClient_SettingsResource extends FAPIClient_Resource
	{

		public function __construct(FAPIClient_PestJSON $client, FAPIClient $parent)
		{
			parent::__construct($client, $parent);
			$this->url = '/settings';
		}

		/**
		 * @param int $user
		 * @return mixed[]
		 */
		public function getAll($user = null)
		{
			try {
				$url = $this->url;

				if (isset($user)) {
					$url .= '?' . \http_build_query([
							'user' => $user,
						], '', '&');
				}

				$response = $this->client->get($url);
				$this->setCode();

				return $response['settings'];
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

		public function get($key)
		{
			try {
				$response = $this->client->get($this->url . '/' . $key);
				$this->setCode();

				return $response['value'];
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

		public function create($data)
		{
			try {
				$response = $this->client->post($this->url, $data);
				$this->setCode();

				return $response['value'];
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

		public function update($key, $data)
		{
			try {
				$response = $this->client->put($this->url . '/' . $key, $data);
				$this->setCode();

				return $response['value'];
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

		public function delete($key)
		{
			try {
				$this->client->delete($this->url . '/' . $key);
				$this->setCode();

				return null;
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

	}

	class FAPIClient_UserResource extends FAPIClient_Resource
	{

		public function __construct(FAPIClient_PestJSON $client, FAPIClient $parent)
		{
			parent::__construct($client, $parent);
			$this->url = '/users';
		}

		public function getProfile()
		{
			try {
				$response = $this->client->get('/user');
				$this->setCode();

				return $response;
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

	}

	class FAPIClient_PaymentResource extends FAPIClient_Resource
	{

		public function __construct(FAPIClient_PestJSON $client, FAPIClient $parent)
		{
			parent::__construct($client, $parent);
			$this->url = '/payments';
		}

		/**
		 * @param integer $limit
		 * @param integer $offset
		 * @param string $order
		 * @param string $date
		 * @param string $search
		 * @param bool $unpaired
		 * @param bool $hidden
		 * @param bool $detailed
		 * @return mixed[][]
		 */
		public function getAll(
			$limit = null,
			$offset = null,
			$order = null,
			$date = null,
			$search = null,
			$unpaired = null,
			$hidden = null,
			$detailed = null
		) {
			try {
				$url = $this->url;

				if (isset($limit) || isset($offset) || isset($order) || isset($date) || isset($search) || isset($unpaired) || isset($hidden) || isset($detailed)) {
					$parameters = [
						'limit' => $limit,
						'offset' => $offset,
						'order' => $order,
						'date' => $date,
						'search' => $search,
						'unpaired' => $unpaired,
						'hidden' => $hidden,
						'detailed' => $detailed,
					];

					$url .= \sprintf('?%s', \http_build_query($parameters, '', '&'));
				}

				$response = $this->client->get($url);
				$this->setCode();

				return $response['payments'];
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

		/**
		 * @param string $date
		 * @param string $search
		 * @param bool $unpaired
		 * @return integer
		 */
		public function count($date = null, $search = null, $unpaired = null)
		{
			try {
				$url = \sprintf('%s/count', $this->url);

				if (isset($date) || isset($search) || isset($unpaired)) {
					$parameters = [
						'date' => $date,
						'search' => $search,
						'unpaired' => $unpaired,
					];

					$url .= \sprintf('?%s', \http_build_query($parameters, '', '&'));
				}

				$response = $this->client->get($url);
				$this->setCode();

				return $response['count'];
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

		public function delete($id)
		{
			throw new FAPIClient_InvalidActionException;
		}

	}

	class FAPIClient_ValidatorResource extends FAPIClient_Resource
	{

		public function __construct(FAPIClient_PestJSON $client, FAPIClient $parent)
		{
			parent::__construct($client, $parent);
			$this->url = '/validators';
		}

		/**
		 * @param string $type
		 * @param mixed $value
		 * @return string
		 */
		public function validate($type, $value)
		{
			try {
				$url = \sprintf('%s/%s', $this->url, $type);

				$data = ['value' => $value];

				$response = $this->client->post($url, $data);

				$this->setCode();

				return $response;
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}

	}

	class FAPIClient_FormResource extends FAPIClient_Resource
	{

		public function __construct(FAPIClient_PestJSON $client, FAPIClient $parent)
		{
			parent::__construct($client, $parent);
			$this->url = '/forms';
		}
	}

	class FAPIClient_OrderResource extends FAPIClient_Resource
	{

		public function __construct(FAPIClient_PestJSON $client, FAPIClient $parent)
		{
			parent::__construct($client, $parent);
			$this->url = '/orders';
		}
	}

	class FAPIClient_ItemTemplateResource extends FAPIClient_Resource
	{

		public function __construct(FAPIClient_PestJSON $client, FAPIClient $parent)
		{
			parent::__construct($client, $parent);
			$this->url = '/item-templates';
		}
	}

	class FAPIClient_StatisticsResource extends FAPIClient_Resource
	{

		public function __construct(FAPIClient_PestJSON $client, FAPIClient $parent)
		{
			parent::__construct($client, $parent);
			$this->url = '/statistics';
		}

		/**
		 * @param int[] $clientIds
		 * @return mixed[][]
		 */
		public function getClientTotals($clientIds)
		{
			try {
				$response = $this->client->post($this->url . '/client-totals', [
					'clients' => $clientIds,
				]);
				$this->setCode();

				return $response;
			} catch (FAPIClient_Pest_Exception $exception) {
				$this->parent->processException($exception);
			}
		}
	}

	class FAPIClient_ExchangeRateSettingResource extends FAPIClient_Resource
	{

		public function __construct(FAPIClient_PestJSON $client, FAPIClient $parent)
		{
			parent::__construct($client, $parent);
			$this->url = '/exchange-rate-settings';
		}
	}

	class FAPIClient_ForeignVatSettingResource extends FAPIClient_Resource
	{

		public function __construct(FAPIClient_PestJSON $client, FAPIClient $parent)
		{
			parent::__construct($client, $parent);
			$this->url = '/foreign-vat-settings';
		}
	}

	class FAPIClient_Pest_Exception extends Exception
	{

	}

	class FAPIClient_Pest_UnknownResponse extends FAPIClient_Pest_Exception
	{

	}

	class FAPIClient_Pest_ClientError extends FAPIClient_Pest_Exception
	{

	}

	class FAPIClient_Pest_BadRequest extends FAPIClient_Pest_ClientError
	{

	}

	class FAPIClient_Pest_Unauthorized extends FAPIClient_Pest_ClientError
	{

	}

	class FAPIClient_Pest_Forbidden extends FAPIClient_Pest_ClientError
	{

	}

	class FAPIClient_Pest_NotFound extends FAPIClient_Pest_ClientError
	{

	}

	class FAPIClient_Pest_MethodNotAllowed extends FAPIClient_Pest_ClientError
	{

	}

	class FAPIClient_Pest_Conflict extends FAPIClient_Pest_ClientError
	{

	}

	class FAPIClient_Pest_Gone extends FAPIClient_Pest_ClientError
	{

	}

	class FAPIClient_Pest_InvalidRecord extends FAPIClient_Pest_ClientError
	{

	}

	class FAPIClient_Pest_ServerError extends FAPIClient_Pest_Exception
	{

	}

	class FAPIClient_Exception extends Exception
	{

	}

	class FAPIClient_InvalidActionException extends FAPIClient_Exception
	{

	}

	class FAPIClient_UnknownResponseException extends FAPIClient_Exception
	{

	}

	class FAPIClient_ClientErrorException extends FAPIClient_Exception
	{

	}

	class FAPIClient_BadRequestException extends FAPIClient_ClientErrorException
	{

	}

	class FAPIClient_UnauthorizedException extends FAPIClient_ClientErrorException
	{

	}

	class FAPIClient_ForbiddenException extends FAPIClient_ClientErrorException
	{

	}

	class FAPIClient_NotFoundException extends FAPIClient_ClientErrorException
	{

	}

	class FAPIClient_MethodNotAllowedException extends FAPIClient_ClientErrorException
	{

	}

	class FAPIClient_ConflictException extends FAPIClient_ClientErrorException
	{

	}

	class FAPIClient_GoneException extends FAPIClient_ClientErrorException
	{

	}

	class FAPIClient_InvalidRecordException extends FAPIClient_ClientErrorException
	{

	}

	class FAPIClient_ServerErrorException extends FAPIClient_Exception
	{

	}

}
