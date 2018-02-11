<?php

namespace SmartSelling\FapiClient\Utils\DateTime;

final class DateTimeHelper
{

	/**
	 * Calculates difference between dates $first and $second in days.
	 * @param \DateTimeInterface $first
	 * @param \DateTimeInterface $second
	 * @return int
	 */
	public static function diffDays(\DateTimeInterface $first, \DateTimeInterface $second)
	{
		$first = new \DateTime($first->format('Y-m-d') . ' 00:00:00');
		$second = new \DateTime($second->format('Y-m-d') . ' 00:00:00');
		$diff = $second->diff($first);
		return $diff->days * ($diff->invert ? -1 : 1);
	}

	/**
	 * @param \DateTimeInterface $first
	 * @param \DateTimeInterface $second
	 * @return bool
	 */
	public static function isGreater(\DateTimeInterface $first, \DateTimeInterface $second)
	{
		return self::diffDays($first, $second) > 0;
	}

	/**
	 * @param \DateTimeInterface $dateTime
	 * @return string
	 */
	public static function formatDate(\DateTimeInterface $dateTime)
	{
		return $dateTime->format('Y-m-d');
	}

}
