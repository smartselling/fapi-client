<?php

namespace SmartSelling\FapiClient\Accrual;

use SmartSelling\FapiClient\Entities\Entity;
use SmartSelling\FapiClient\Entities\IdIdentifiedTrait;
use SmartSelling\FapiClient\Utils\DateTime\DateRange;
use SmartSelling\FapiClient\Utils\DateTime\DateTimeHelper;
use SmartSelling\Parameters\Parameters;

final class Accrual extends Entity
{

	use IdIdentifiedTrait;

	/** @var bool */
	private $downloaded;

	/** @var bool */
	private $completed;

	/** @var bool */
	private $deleted;

	/** @var DateRange */
	private $dateRange;

	/** @var int|null */
	private $userId;

	/** @var bool */
	private $failed;

	/**
	 * @param array $data
	 */
	public function __construct(array $data)
	{
		$parameters = Parameters::from($data);
		$this->id = $parameters->getIntOrNull('id');
		$this->userId = $parameters->getIntOrNull('user_id');

		$from = $parameters->getDate('from', Parameters::REQUIRED);
		$to = $parameters->getDate('to', Parameters::REQUIRED);
		$this->dateRange = DateRange::from($from, $to);

		$this->deleted = $parameters->getBool('deleted');
		$this->completed = $parameters->getBool('completed');
		$this->downloaded = $parameters->getBool('downloaded');
		$this->failed = $parameters->getBool('failed');
	}

	/**
	 * @return bool
	 */
	public function isDownloaded()
	{
		return $this->downloaded;
	}

	/**
	 * @return bool
	 */
	public function isCompleted()
	{
		return $this->completed;
	}

	/**
	 * @return bool
	 */
	public function isDeleted()
	{
		return $this->deleted;
	}

	/**
	 * @return DateRange
	 */
	public function getDateRange()
	{
		return $this->dateRange;
	}

	/**
	 * @return int|null
	 */
	public function getUserId()
	{
		return $this->userId;
	}

	/**
	 * @return bool
	 */
	public function isFailed()
	{
		return $this->failed;
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		return [
			'id' => $this->id,
			'user_id' => $this->userId,
			'from' => DateTimeHelper::formatDate($this->dateRange->getFrom()),
			'to' => DateTimeHelper::formatDate($this->dateRange->getTo()),
			'deleted' => $this->deleted,
			'completed' => $this->completed,
			'downloaded' => $this->downloaded,
			'failed' => $this->failed,
		];
	}
}
