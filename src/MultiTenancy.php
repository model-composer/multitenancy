<?php namespace Model\Multitenancy;

use Model\Config\Config;

class MultiTenancy
{
	private static ?int $tenant = null;
	private static array $ignoredTables = [];

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
	 * Adds a table to ignore in runtime
	 *
	 * @param string $database
	 * @param string $table
	 * @return void
	 */
	public static function ignoreTable(string $database, string $table): void
	{
		if (!isset(self::$ignoredTables[$database]))
			self::$ignoredTables[$database] = [];
		if (!in_array($table, self::$ignoredTables[$database]))
			self::$ignoredTables[$database][] = $table;
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

			$ignoredTables = array_merge($dbConfig['ignore_tables'], self::$ignoredTables[$database] ?? []);
			if ($table === null or !in_array($table, $ignoredTables))
				return $dbConfig['column'];
		}

		return null;
	}
}
