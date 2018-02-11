<?php

namespace SmartSelling\FapiClient\Filters;

abstract class GenericFilterBuilder
{

	/**
	 * @return GenericFilterObject
	 */
	abstract public function build();
}
