<?php

namespace SmartSelling\FapiClient\Accrual;

use SmartSelling\FapiClient\Base\BaseClient;
use SmartSelling\FapiClient\Rest\FapiRestClient;

final class AccrualClient extends BaseClient
{

	public function __construct(FapiRestClient $fapiClient)
	{
		parent::__construct($fapiClient, '/accrual', 'accruals');
	}

	/**
	 * @param Accrual $accrual
	 * @return Accrual
	 * @throws \SmartSelling\FapiClient\Rest\AuthorizationException
	 * @throws \SmartSelling\FapiClient\Rest\MethodNotAllowedException
	 */
	public function create(Accrual $accrual)
	{
		return new Accrual($this->getClient()->createResource($this->getResourceName(), $accrual->toArray()));
	}

	/**
	 * @return null|Accrual
	 * @throws \SmartSelling\FapiClient\Rest\AuthorizationException
	 * @throws \SmartSelling\FapiClient\Rest\MethodNotAllowedException
	 */
	public function getPending()
	{
		$data = $this->getClient()->getSingularResource($this->getResourceName() . '/pending', []);
		if (!$data) {
			return null;
		}
		return new Accrual($data);
	}

	/**
	 * @param $id
	 * @return \SmartSelling\HttpClient\HttpResponse
	 * @throws \SmartSelling\FapiClient\Rest\AuthorizationException
	 * @throws \SmartSelling\FapiClient\Rest\MethodNotAllowedException
	 */
	public function download($id)
	{
		return $this->getClient()->getFile($id, $this->getResourceName(). '/download');
	}

}
