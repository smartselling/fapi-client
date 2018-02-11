<?php

namespace SmartSelling\FapiClient\Base;

use SmartSelling\FapiClient\Rest\FapiRestClient;

abstract class BaseClient
{

	/** @var string */
	private $resourceName;

	/** @var string */
	private $resourcesKey;

	/** @var FapiRestClient */
	private $fapiClient;

	/**
	 * @param FapiRestClient $fapiClient
	 * @param string $resourceName
	 * @param string $resourcesKey
	 */
	public function __construct(FapiRestClient $fapiClient, $resourceName, $resourcesKey)
	{
		$this->fapiClient = $fapiClient;
		$this->resourceName = $resourceName;
		$this->resourcesKey = $resourcesKey;
	}

	/**
	 * @return string
	 */
	public function getResourceName()
	{
		return $this->resourceName;
	}

	/**
	 * @return string
	 */
	public function getResourcesKey()
	{
		return $this->resourcesKey;
	}

	/**
	 * @return FapiRestClient
	 */
	public function getClient()
	{
		return $this->fapiClient;
	}

}
