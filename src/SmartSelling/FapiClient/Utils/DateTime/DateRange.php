<?php

namespace SmartSelling\FapiClient\Utils\DateTime;

final class DateRange
{

	/** @var \DateTimeImmutable */
	private $from;

	/** @var \DateTimeImmutable */
	private $to;

	/**
	 * @param \DateTime $from
	 * @param \DateTime $to
	 * @return DateRange
	 */
	public static function from(\DateTime $from, \DateTime $to)
	{
		$immutableFrom = \DateTimeImmutable::createFromMutable($from);
		$immutableTo = \DateTimeImmutable::createFromMutable($to);

		/** @noinspection ExceptionsAnnotatingAndHandlingInspection */
		return new DateRange($immutableFrom, $immutableTo);
	}

	/**
	 * @param string $from
	 * @param string $to
	 * @return DateRange
	 */
	public static function fromString($from, $to)
	{
		$immutableFrom = new \DateTimeImmutable($from);
		$immutableTo = new \DateTimeImmutable($to);

		/** @noinspection ExceptionsAnnotatingAndHandlingInspection */
		return new DateRange($immutableFrom, $immutableTo);
	}

	/**
	 * @param \DateTimeImmutable $from
	 * @param \DateTimeImmutable $to
	 * @throws InvalidDateRangeException
	 */
	public function __construct(\DateTimeImmutable $from, \DateTimeImmutable $to)
	{
		$this->from = $from;
		$this->to = $to;

		$this->normalize();

		if (!self::isValidRange($this->from, $this->to)) {
			/** @noinspection PhpUnhandledExceptionInspection ThrowRawExceptionInspection */
			throw new InvalidDateRangeException();
		}
	}

	/**
	 * @param \DateTimeInterface $from
	 * @param \DateTimeInterface $to
	 * @return bool
	 */
	private static function isValidRange(\DateTimeInterface $from, \DateTimeInterface $to)
	{
		return DateTimeHelper::isGreater($to, $from);
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function getFrom()
	{
		return $this->from;
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function getTo()
	{
		return $this->to;
	}

	/**
	 * @return int
	 */
	public function getDays()
	{
		return DateTimeHelper::diffDays($this->to, $this->from);
	}

	private function normalize()
	{
		$this->from = $this->from->setTime(0, 0, 0);
		$this->to = $this->to->setTime(0, 0, 0);
	}

}
