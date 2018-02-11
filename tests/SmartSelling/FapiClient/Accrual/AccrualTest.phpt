<?php

namespace SmartSelling\FapiClient\Accrual;

use SmartSelling\FapiClient\FapiClient;
use SmartSelling\FapiClient\Rest\FapiRestClient;
use SmartSelling\HttpClient\CapturingHttpClient;
use SmartSelling\HttpClient\GuzzleHttpClient;
use SmartSelling\HttpClient\HttpResponse;
use Tester\Assert;
use Tester\Environment;
use Tester\TestCase;

require __DIR__ . '/../../../bootstrap.php';
require __DIR__ . '/MockHttpClient/AccrualMockHttpClient.php';


/**
 * @testCase
 */
final class AccrualTest extends TestCase
{

	/**
	 * @var bool
	 */
	private $generateMockHttpClient = false;

	/**
	 * @var CapturingHttpClient|AccrualMockHttpClient
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
			$this->httpClient = new AccrualMockHttpClient();
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
				__DIR__ . '/MockHttpClient/AccrualMockHttpClient.php',
				AccrualMockHttpClient::class
			);
		}
	}

	public function testAccrual()
	{
		$accrual = new Accrual([
			'from' => '2017-01-01',
			'to' => '2017-12-31',
		]);

		$accrual = $this->fapiClient->getAccrual()->create($accrual);
		Assert::type('int', $accrual->getId());

		$accrualPending = $this->fapiClient->getAccrual()->getPending();
		Assert::type('int', $accrualPending->getId());
		Assert::equal($accrual->getId(), $accrualPending->getId());

		$response = $this->fapiClient->getAccrual()->download($accrualPending->getId());
		Assert::type(HttpResponse::class, $response);
		Assert::notEqual('null', $response->getBody());
	}

}


/** @noinspection PhpUnhandledExceptionInspection */
(new AccrualTest())->run();
