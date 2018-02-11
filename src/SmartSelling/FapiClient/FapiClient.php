<?php

namespace SmartSelling\FapiClient;

use SmartSelling\FapiClient\Accrual\AccrualClient;
use SmartSelling\FapiClient\Banks\BankClient;
use SmartSelling\FapiClient\Rest\AuthorizationException;
use SmartSelling\FapiClient\Rest\FapiRestClient;

final class FapiClient
{

	/** @var FapiRestClient */
	private $fapiRestClient;

	/** @var BankClient */
	private $bank;

	/** @var AccrualClient */
	private $accrual;

	public function __construct(FapiRestClient $fapiRestClient)
	{
		$this->bank = new BankClient($fapiRestClient);
		$this->accrual = new AccrualClient($fapiRestClient);
		$this->fapiRestClient = $fapiRestClient;
	}

	/**
	 * @return array
	 * @throws AuthorizationException
	 */
	public function checkConnection()
	{
		return $this->fapiRestClient->checkConnection();
	}

	/**
	 * @return BankClient
	 */
	public function getBank()
	{
		return $this->bank;
	}

	/**
	 * @return AccrualClient
	 */
	public function getAccrual()
	{
		return $this->accrual;
	}

}
