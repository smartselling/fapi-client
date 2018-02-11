<?php

namespace SmartSelling\FapiClient\Invoices;

use SmartSelling\FapiClient\Filters\GenericFilterObject;
use SmartSelling\Parameters\Parameters;

final class InvoiceFilter extends GenericFilterObject
{

	/** @var array */
	private $fields;

	/** @var array */
	private $conditions;

	/**
	 * @param array $data
	 */
	public function __construct(array $data)
	{
		$parameters = Parameters::from($data);
		$this->conditions = [];

		if ($parameters->hasKey('status')) {
			$this->conditions['status'] = $parameters->getString('status');
		}

		if ($parameters->hasKey('types')) {
			$this->conditions['types'] = $parameters->getStringList('types');
		}

	}

	/**
	 * @return array
	 */
	public function getFields()
	{
		return $this->fields;
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		return $this->conditions;
	}
}
