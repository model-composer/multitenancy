<?php namespace Model\MultiTenancy;

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
	 * Config retriever
	 *
	 * @return array
	 * @throws \Exception
	 */
	public static function getConfig(): array
	{
		return Config::get('multitenancy', [
			[
				'version' => '0.1.0',
				'migration' => function (array $config, string $env) {
					if ($config) // Already existing
						return $config;

					return [
						'databases' => [
							'primary' => [
								'enabled' => false,
								'column' => 'user',
								'ignore_tables' => [],
							],
						],
					];
				},
			],
		]);
	}
}
