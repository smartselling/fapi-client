<?php

namespace SmartSelling\FapiClient\Banks;

use SmartSelling\FapiClient\Base\BaseClient;
use SmartSelling\FapiClient\Rest\FapiRestClient;

final class BankClient extends BaseClient
{

	public function __construct(FapiRestClient $fapiClient)
	{
		parent::__construct($fapiClient, '/banks', 'banks');
	}

	/**
	 * @return array|Bank[]
	 */
	public function getAll()
	{
		$resources = $this->getClient()->getResources($this->getResourceName(), $this->getResourcesKey());
		return $this->toEntitiesArray($resources);
	}

	/**
	 * @param int $id
	 * @return Bank
	 */
	public function get($id)
	{
		$resource = $this->getClient()->getResource($this->getResourceName(), $id);
		return $this->toEntity($resource);
	}

	/**
	 * @param array $data
	 * @return Bank
	 */
	private function toEntity(array $data)
	{
		return new Bank($data);
	}

	/**
	 * @param array $data
	 * @return array|Bank[]
	 */
	private function toEntitiesArray(array $data)
	{
		$entities = [];
		foreach ($data as $item) {
			$entities[] = $this->toEntity($item);
		}
		return $entities;
	}
}
