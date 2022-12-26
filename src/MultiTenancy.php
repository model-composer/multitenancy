<?php namespace Model\Multitenancy;

use Model\Config\Config;

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

	/**
	 * @param string $database
	 * @param string|null $table
	 * @return string|null
	 */
	public static function getTenantColumn(string $database, ?string $table = null): ?string
	{
		$config = Config::get('multitenancy');

		if (isset($config['databases'][$database]) and $config['databases'][$database]['enabled']) {
			$dbConfig = $config['databases'][$database];

			if ($table === null or !in_array($table, $dbConfig['ignore_tables']))
				return $dbConfig['column'];
		}

		return null;
	}
}
