<?php

namespace SmartSelling\FapiClient\Entities;

trait IdIdentifiedTrait
{

	/** @var int */
	private $id;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function __clone()
	{
		$this->id = null;
	}
}
