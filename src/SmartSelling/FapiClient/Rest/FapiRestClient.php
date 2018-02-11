<?php

namespace SmartSelling\FapiClient\Rest;

use InvalidArgumentException;
use SmartSelling\HttpClient\HttpClientException;
use SmartSelling\HttpClient\HttpMethod;
use SmartSelling\HttpClient\HttpRequest;
use SmartSelling\HttpClient\HttpResponse;
use SmartSelling\HttpClient\HttpStatusCode;
use SmartSelling\HttpClient\IHttpClient;
use SmartSelling\HttpClient\Rest\InvalidResponseBodyException;
use SmartSelling\HttpClient\Rest\InvalidStatusCodeException;
use SmartSelling\HttpClient\Rest\RestClientException;
use SmartSelling\HttpClient\Utils\Json;

final class FapiRestClient
{

	/**
	 * @var string
	 */
	private $username;

	/**
	 * @var string
	 */
	private $password;

	/**
	 * @var string
	 */
	private $apiUrl;

	/**
	 * @var IHttpClient
	 */
	private $httpClient;

	/**
	 * @param string $username
	 * @param string $password
	 * @param string $apiUrl
	 * @param IHttpClient $httpClient
	 */
	public function __construct($username, $password, $apiUrl, IHttpClient $httpClient)
	{
		$this->username = $username;
		$this->password = $password;
		$this->apiUrl = rtrim($apiUrl, '/');
		$this->httpClient = $httpClient;
	}

	/**
	 * @return string
	 */
	public function getCurrentUsername()
	{
		return $this->username;
	}

	/**
	 * @param string $path
	 * @param int $option
	 * @return array
	 */
	public function checkConnection($path = '/check-connection', $option = 0)
	{
		$httpResponse = $this->sendHttpRequest(HttpMethod::GET, $path);

		if ($httpResponse->getStatusCode() === HttpStatusCode::S200_OK) {
			return $this->getResourceResponseData($httpResponse, $option);
		}

		if ($httpResponse->getStatusCode() === HttpStatusCode::S405_METHOD_NOT_ALLOWED) {
			$message = $this->getErrorMessage($httpResponse);
			throw new MethodNotAllowedException($message);
		}

		if ($httpResponse->getStatusCode() === HttpStatusCode::S401_UNAUTHORIZED) {
			$message = $this->getErrorMessage($httpResponse);
			throw new AuthorizationException($message);
		}

		if ($httpResponse->getStatusCode() === HttpStatusCode::S404_NOT_FOUND) {
			return null;
		}

		throw new InvalidStatusCodeException();

	}

	/**
	 * @param string $path
	 * @param string $resourcesKey
	 * @param array $parameters
	 * @param int $options
	 * @return array
	 */
	public function getResources($path, $resourcesKey, array $parameters = [], $options = 0)
	{
		if ($parameters) {
			$path .= '?' . $this->formatUrlParameters($parameters);
		}

		$httpResponse = $this->sendHttpRequest(HttpMethod::GET, $path);

		if ($httpResponse->getStatusCode() === HttpStatusCode::S200_OK) {
			return $this->getResourcesResponseData($httpResponse, $resourcesKey, $options);
		}

		if ($httpResponse->getStatusCode() === HttpStatusCode::S401_UNAUTHORIZED) {
			$message = $this->getErrorMessage($httpResponse);
			throw new AuthorizationException($message);
		}

		if ($httpResponse->getStatusCode() === HttpStatusCode::S405_METHOD_NOT_ALLOWED) {
			$message = $this->getErrorMessage($httpResponse);
			throw new MethodNotAllowedException($message);
		}

		throw new InvalidStatusCodeException();
	}

	/**
	 * @param string $path
	 * @param string|int $id
	 * @param array $parameters
	 * @param int $options
	 * @return array|null
	 */
	public function getResource($path, $id, array $parameters = [], $options = 0)
	{
		$this->validateId($id, $options);

		$path .= '/' . $id;

		if ($parameters) {
			$path .= '?' . $this->formatUrlParameters($parameters);
		}

		$httpResponse = $this->sendHttpRequest(HttpMethod::GET, $path);

		if ($httpResponse->getStatusCode() === HttpStatusCode::S200_OK) {
			return $this->getResourceResponseData($httpResponse, $options);
		}

		if ($httpResponse->getStatusCode() === HttpStatusCode::S401_UNAUTHORIZED) {
			$message = $this->getErrorMessage($httpResponse);
			throw new AuthorizationException($message);
		}

		if ($httpResponse->getStatusCode() === HttpStatusCode::S404_NOT_FOUND) {
			return null;
		}

		if ($httpResponse->getStatusCode() === HttpStatusCode::S405_METHOD_NOT_ALLOWED) {
			$message = $this->getErrorMessage($httpResponse);
			throw new MethodNotAllowedException($message);
		}

		throw new InvalidStatusCodeException();
	}

	/**
	 * @param string $path
	 * @param array $parameters
	 * @param int $options
	 * @return array
	 */
	public function getSingularResource($path, array $parameters = [], $options = 0)
	{
		if ($parameters) {
			$path .= '?' . $this->formatUrlParameters($parameters);
		}

		$httpResponse = $this->sendHttpRequest(HttpMethod::GET, $path);

		if ($httpResponse->getStatusCode() === HttpStatusCode::S200_OK) {
			return $this->getResourceResponseData($httpResponse, $options);
		}

		if ($httpResponse->getStatusCode() === HttpStatusCode::S401_UNAUTHORIZED) {
			$message = $this->getErrorMessage($httpResponse);
			throw new AuthorizationException($message);
		}

		if ($httpResponse->getStatusCode() === HttpStatusCode::S405_METHOD_NOT_ALLOWED) {
			$message = $this->getErrorMessage($httpResponse);
			throw new MethodNotAllowedException($message);
		}

		throw new InvalidStatusCodeException();
	}

	/**
	 * @param string $path
	 * @param array $data
	 * @param int $options
	 * @return array
	 */
	public function createResource($path, array $data, $options = 0)
	{
		$httpResponse = $this->sendHttpRequest(HttpMethod::POST, $path, $data);

		if ($httpResponse->getStatusCode() === HttpStatusCode::S201_CREATED) {
			return $this->getResourceResponseData($httpResponse, $options);
		}

		if ($httpResponse->getStatusCode() === HttpStatusCode::S401_UNAUTHORIZED) {
			$message = $this->getErrorMessage($httpResponse);
			throw new AuthorizationException($message);
		}

		if ($httpResponse->getStatusCode() === HttpStatusCode::S405_METHOD_NOT_ALLOWED) {
			$message = $this->getErrorMessage($httpResponse);
			throw new MethodNotAllowedException($message);
		}

		if ($httpResponse->getStatusCode() === HttpStatusCode::S400_BAD_REQUEST) {
			$message = $this->getErrorMessage($httpResponse);
			throw new BadRequestException($message);
		}

		if ($httpResponse->getStatusCode() === HttpStatusCode::S500_INTERNAL_SERVER_ERROR) {
			$message = $this->getErrorMessage($httpResponse);
			throw new ServerErrorException($message);
		}


		throw new InvalidStatusCodeException();
	}

	/**
	 * @param string $path
	 * @param int $id
	 * @param array $data
	 * @param int $options
	 * @return array
	 */
	public function updateResource($path, $id, array $data, $options = 0)
	{
		$this->validateId($id, $options);

		$httpResponse = $this->sendHttpRequest(HttpMethod::PUT, $path . '/' . $id, $data);

		if ($httpResponse->getStatusCode() === HttpStatusCode::S200_OK) {
			return $this->getResourceResponseData($httpResponse, $options);
		}

		if ($httpResponse->getStatusCode() === HttpStatusCode::S401_UNAUTHORIZED) {
			$message = $this->getErrorMessage($httpResponse);
			throw new AuthorizationException($message);
		}

		if ($httpResponse->getStatusCode() === HttpStatusCode::S405_METHOD_NOT_ALLOWED) {
			$message = $this->getErrorMessage($httpResponse);
			throw new MethodNotAllowedException($message);
		}

		throw new InvalidStatusCodeException();
	}

	/**
	 * @param string $path
	 * @param int $id
	 * @param int $options
	 * @return void
	 */
	public function deleteResource($path, $id, $options = 0)
	{
		$this->validateId($id, $options);

		$httpResponse = $this->sendHttpRequest(HttpMethod::DELETE, $path . '/' . $id);

		if ($httpResponse->getStatusCode() === HttpStatusCode::S401_UNAUTHORIZED) {
			$message = $this->getErrorMessage($httpResponse);
			throw new AuthorizationException($message);
		}

		if ($httpResponse->getStatusCode() === HttpStatusCode::S405_METHOD_NOT_ALLOWED) {
			$message = $this->getErrorMessage($httpResponse);
			throw new MethodNotAllowedException($message);
		}

		if ($httpResponse->getStatusCode() !== HttpStatusCode::S200_OK) {
			throw new InvalidStatusCodeException();
		}
	}

	/**
	 * @param int $id
	 * @param string $path
	 * @param int $options
	 * @return HttpResponse
	 * @throws AuthorizationException
	 * @throws MethodNotAllowedException
	 */
	public function getFile($id, $path, $options = 0)
	{
		$this->validateId($id, $options);

		$httpResponse = $this->sendHttpRequest(HttpMethod::GET, $path . '/' . $id);

		if ($httpResponse->getStatusCode() === HttpStatusCode::S401_UNAUTHORIZED) {
			$message = $this->getErrorMessage($httpResponse);
			throw new AuthorizationException($message);
		}

		if ($httpResponse->getStatusCode() === HttpStatusCode::S405_METHOD_NOT_ALLOWED) {
			$message = $this->getErrorMessage($httpResponse);
			throw new MethodNotAllowedException($message);
		}

		if ($httpResponse->getStatusCode() !== HttpStatusCode::S200_OK) {
			throw new InvalidStatusCodeException();
		}

		return $httpResponse;
	}

	/**
	 * @param string|int $id
	 * @param int $options
	 * @return void
	 * @throws InvalidArgumentException
	 */
	private function validateId($id, $options)
	{
		if ($options & FapiRestClientOptions::STRING_KEY) {
			if (!is_string($id)) {
				throw new InvalidArgumentException('Parameter id must be a string.');
			}
		} else {
			if (!is_int($id)) {
				throw new InvalidArgumentException('Parameter id must be an integer.');
			}
		}
	}

	/**
	 * @param string $method
	 * @param string $path
	 * @param array|null $data
	 * @param array $headers
	 * @return HttpResponse
	 */
	private function sendHttpRequest($method, $path, $data = null, array $headers = [])
	{
		$url = $this->apiUrl . $path;

		if (!isset($headers['Content-Type'])) {
			$headers['Content-Type'] = 'application/json';
		}

		if (!isset($headers['Accept'])) {
			$headers['Accept'] = 'application/json';
		}

		$options = [
			'auth' => [$this->username, $this->password],
			'headers' => $headers,
		];

		if ($data !== null) {
			$options['json'] = $data;
		}

		try {
			$httpRequest = new HttpRequest($url, $method, $options);
			return $this->httpClient->sendHttpRequest($httpRequest);
		} catch (HttpClientException $e) {
			throw new RestClientException('Failed to send an HTTP request.', 0, $e);
		}
	}

	/**
	 * @param array $parameters
	 * @return string
	 */
	private function formatUrlParameters(array $parameters)
	{
		return http_build_query($parameters, '', '&');
	}

	/**
	 * @param HttpResponse $httpResponse
	 * @param string $resourcesKey
	 * @param int $options
	 * @return array
	 */
	private function getResourcesResponseData(HttpResponse $httpResponse, $resourcesKey, $options)
	{
		$responseData = $this->getResponseData($httpResponse);

		if (!is_array($responseData)) {
			throw new InvalidResponseBodyException('Response data is not an array.');
		}

		if (!isset($responseData[$resourcesKey])) {
			throw new InvalidResponseBodyException('Response data does not contain attribute with resources.');
		}

		$resources = $responseData[$resourcesKey];

		if (!is_array($resources)) {
			throw new InvalidResponseBodyException('Resources must be an array.');
		}

		foreach ($resources as $resource) {
			$this->validateResource($resource, $options);
		}

		return $resources;
	}

	/**
	 * @param HttpResponse $httpResponse
	 * @param int $options
	 * @return array|string
	 */
	private function getResourceResponseData($httpResponse, $options)
	{
		$resource = $this->getResponseData($httpResponse);

		$this->validateResource($resource, $options);

		return $resource;
	}

	/**
	 * @param array|string $resource
	 * @param int $options
	 * @return void
	 */
	private function validateResource($resource, $options)
	{
		if ($options & FapiRestClientOptions::STRING_RESOURCE) {
			if (!is_string($resource)) {
				throw new InvalidResponseBodyException('Resource must be a string.');
			}
		} else {
			if (!is_array($resource)) {
				throw new InvalidResponseBodyException('Resource must be an array.');
			}
		}
	}

	/**
	 * @param HttpResponse $httpResponse
	 * @return string|null
	 */
	private function getErrorMessage(HttpResponse $httpResponse)
	{
		$responseData = $this->getResponseData($httpResponse);
		return isset($responseData['error']['message']) ? $responseData['error']['message'] : '';
	}

	/**
	 * @param HttpResponse $httpResponse
	 * @return mixed
	 */
	private function getResponseData(HttpResponse $httpResponse)
	{
		try {
			return Json::decode($httpResponse->getBody(), Json::FORCE_ARRAY);
		} catch (\Exception $e) {
			throw new InvalidResponseBodyException('Response body is not a valid JSON.', 0, $e);
		}
	}
}
