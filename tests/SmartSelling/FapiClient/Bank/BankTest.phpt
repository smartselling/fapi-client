<?php

namespace SmartSelling\FapiClient\Banks;

use SmartSelling\FapiClient\Bank\BankMockHttpClient;
use SmartSelling\FapiClient\FapiClient;
use SmartSelling\FapiClient\Rest\FapiRestClient;
use SmartSelling\HttpClient\CapturingHttpClient;
use SmartSelling\HttpClient\GuzzleHttpClient;
use Tester\Assert;
use Tester\Environment;
use Tester\TestCase;

require __DIR__ . '/../../../bootstrap.php';
require __DIR__ . '/MockHttpClient/BankMockHttpClient.php';


/**
 * @testCase
 */
final class BankTest extends TestCase
{

	/**
	 * @var bool
	 */
	private $generateMockHttpClient = false;

	/**
	 * @var CapturingHttpClient|BankMockHttpClient
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
			$this->httpClient = new BankMockHttpClient();
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
				__DIR__ . '/MockHttpClient/BankMockHttpClient.php',
				BankMockHttpClient::class
			);
		}
	}

	public function testBank()
	{
		$bank = $this->fapiClient->getBank()->get(1);
		Assert::equal(1, $bank->getId());

		$banks = $this->fapiClient->getBank()->getAll();
		Assert::type('array', $banks);
	}

}


/** @noinspection PhpUnhandledExceptionInspection */
(new BankTest())->run();
