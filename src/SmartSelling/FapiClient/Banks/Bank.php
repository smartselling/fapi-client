<?php

namespace SmartSelling\FapiClient\Banks;

use SmartSelling\FapiClient\Entities\Entity;
use SmartSelling\FapiClient\Entities\IdIdentifiedTrait;
use SmartSelling\Parameters\Parameters;

final class Bank extends Entity
{

	use IdIdentifiedTrait;

	/** @var string */
	private $name;

	/** @var bool */
	private $api;

	/** @var string */
	private $bankCode;

	/** @var string */
	private $swift;

	public function __construct(array $data)
	{
		$parameters = Parameters::from($data);
		$this->id = $parameters->getInt('id', Parameters::REQUIRED);
		$this->name = $parameters->getString('name', Parameters::REQUIRED);
		$this->api = $parameters->getBool('api', Parameters::REQUIRED);
		$this->bankCode = $parameters->getString('bank_code', Parameters::REQUIRED);
		$this->swift = $parameters->getString('swift', Parameters::REQUIRED);
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return bool
	 */
	public function isApi()
	{
		return $this->api;
	}

	/**
	 * @return string
	 */
	public function getBankCode()
	{
		return $this->bankCode;
	}

	/**
	 * @return string
	 */
	public function getSwift()
	{
		return $this->swift;
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'api' => $this->api,
			'bank_code' => $this->bankCode,
			'swift' => $this->swift,
		];
	}
}
