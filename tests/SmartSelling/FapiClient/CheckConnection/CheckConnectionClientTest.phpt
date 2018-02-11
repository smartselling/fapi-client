<?php

namespace SmartSelling\FapiClient\CheckConnection;

use SmartSelling\FapiClient\FapiClient;
use SmartSelling\FapiClient\Rest\FapiRestClient;
use SmartSelling\HttpClient\CapturingHttpClient;
use SmartSelling\HttpClient\GuzzleHttpClient;
use Tester\Assert;
use Tester\Environment;
use Tester\TestCase;

require __DIR__ . '/../../../bootstrap.php';
require __DIR__ . '/MockHttpClient/CheckConnectionMockHttpClient.php';


/**
 * @testCase
 */
class CheckConnectionClientTest extends TestCase
{

	/**
	 * @var bool
	 */
	private $generateMockHttpClient = false;

	/**
	 * @var CapturingHttpClient|CheckConnectionMockHttpClient
	 */
	private $httpClient;

	/**
	 * @var FapiClient
	 */
	private $fapiClient;

	protected function setUp()
	{
		Environment::lock('FapiClient', LOCKS_DIR);

		if ($this->generateMockHttpClient) {
			$this->httpClient = new CapturingHttpClient(new GuzzleHttpClient());
		} else {
			$this->httpClient = new CheckConnectionMockHttpClient();
		}

		$this->fapiClient = new FapiClient(new FapiRestClient(
				'tester@fapi.cz',
				'xxx',
				'http://api.fapi.log/v2',
				$this->httpClient
			)
		);
	}

	protected function tearDown()
	{
		if ($this->generateMockHttpClient) {
			$this->httpClient->writeToPhpFile(
				__DIR__ . '/MockHttpClient/CheckConnectionMockHttpClient.php',
				CheckConnectionMockHttpClient::class
			);
		}
	}

	public function testConnection()
	{
		$result = $this->fapiClient->checkConnection();
		Assert::type('array', $result);
		Assert::equal('ok', $result['status']);
	}

}


/** @noinspection PhpUnhandledExceptionInspection */
(new CheckConnectionClientTest())->run();
