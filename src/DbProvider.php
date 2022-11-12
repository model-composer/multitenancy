<?php namespace Model\MultiTenancy;

use Model\Config\Config;
use Model\Db\AbstractDbProvider;
use Model\Db\DbConnection;

class DbProvider extends AbstractDbProvider
{
	public static function alterInsert(DbConnection $db, array $queries): array
	{
		foreach ($queries as &$query) {
			foreach ($query['data'] as &$row)
				[$row, $options] = self::alter($db, $query['table'], $row, $query['options']);
			unset($row);
		}

		return $queries;
	}

	public static function alterDelete(DbConnection $db, string $table, array|int $where, array $options): array
	{
		return self::alter($db, $table, $where, $options);
	}

	public static function alterSelect(DbConnection $db, string $table, array|int $where, array $options): array
	{
		return self::alter($db, $table, $where, $options);
	}

	private static function alter(DbConnection $db, string $table, array|int $data, array $options): array
	{
		$config = Config::get('multitenancy');

		if (isset($config['databases'][$db->getName()]) and $config['databases'][$db->getName()]['enabled']) {
			$tableModel = $db->getTable($table);

			if (
				!($options['skip_tenancy'] ?? false)
				and isset($tableModel->columns[$config['column']])
				and !in_array($table, $config['ignore_tables'])
			) {
				$data[$config['column']] = MultiTenancy::getTenant();
			}
		}

		return [$data, $options];
	}
}
