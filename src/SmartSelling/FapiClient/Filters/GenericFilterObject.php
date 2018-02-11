<?php

namespace SmartSelling\FapiClient\Filters;

abstract class GenericFilterObject
{

	/**
	 * @return array
	 */
	abstract public function getFields();

	/**
	 * @return array
	 */
	abstract public function toArray();

}
