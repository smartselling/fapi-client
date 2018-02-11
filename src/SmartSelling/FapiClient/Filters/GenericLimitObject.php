<?php

namespace SmartSelling\FapiClient\Filters;

final class GenericLimitObject
{

	/** @var int */
	private $limit;

	/** @var int|null */
	private $offset;

	/**
	 * @param int $limit
	 * @param int|null $offset
	 * @throws \Exception
	 */
	public function __construct($limit = 100, $offset = null)
	{
		$this->limit = $limit;
		$this->offset = $offset;

		if ($limit > 100) {
			throw new \Exception('Limit cannot be greater then 100.');
		}
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		return [
			'limit' => $this->limit,
			'offset' => $this->offset,
		];
	}

}
