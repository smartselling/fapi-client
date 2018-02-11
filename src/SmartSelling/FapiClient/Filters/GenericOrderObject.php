<?php

namespace SmartSelling\FapiClient\Filters;

final class GenericOrderObject
{

	/** @var string */
	private $order;

	/**
	 * @param string $order
	 */
	public function __construct($order = 'id')
	{
		$this->order = $order;
	}

	/**
	 * @return string
	 */
	public function getOrder()
	{
		return $this->order;
	}
}
