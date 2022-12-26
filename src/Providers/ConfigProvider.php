<?php namespace Model\Multitenancy\Providers;

use Model\Config\AbstractConfigProvider;

class ConfigProvider extends AbstractConfigProvider
{
	public static function migrations(): array
	{
		return [
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
		];
	}
}
