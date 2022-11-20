<?php namespace Model\Multitenancy;

class MultiTenancy
{
	private static ?int $tenant = null;

	/**
	 * @return int|null
	 */
	public static function getTenant(): ?int
	{
		return self::$tenant;
	}

	/**
	 * @param int|null $tenant
	 */
	public static function setTenant(?int $tenant): void
	{
		self::$tenant = $tenant;
	}
}
