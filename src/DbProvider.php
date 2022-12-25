<?php namespace Model\Multitenancy;

use Model\Config\Config;
use Model\Db\AbstractDbProvider;
use Model\Db\DbConnection;

class DbProvider extends AbstractDbProvider
{
	public static function alterInsert(DbConnection $db, array $queries): array
	{
		foreach ($queries as &$query) {
			foreach ($query['rows'] as &$row)
				[$row, $options] = self::alter($db, $query['table'], $row, $query['options']);
			unset($row);
		}

		return $queries;
	}

	public static function alterDelete(DbConnection $db, string $table, array|int $where, array $options): array
	{
		return self::alter($db, $table, $where, $options);
	}

	public static function alterUpdate(DbConnection $db, array $queries): array
	{
		foreach ($queries as &$query) {
			[$where, $options] = self::alter($db, $query['table'], $query['where'], $query['options']);
			$query['where'] = $where;
		}

		return $queries;
	}

	public static function alterSelect(DbConnection $db, string $table, array|int $where, array $options): array
	{
		return self::alter($db, $table, $where, $options);
	}

	private static function alter(DbConnection $db, string $table, array|int $data, array $options): array
	{
		$config = Config::get('multitenancy');

		if (isset($config['databases'][$db->getName()]) and $config['databases'][$db->getName()]['enabled']) {
			$dbConfig = $config['databases'][$db->getName()];
			$tableModel = $db->getTable($table);

			if (
				!($options['skip_tenancy'] ?? false)
				and isset($tableModel->columns[$dbConfig['column']])
				and !in_array($table, $dbConfig['ignore_tables'])
			) {
				if (is_int($data))
					$data = [$tableModel->primary[0] => $data];

				$tenantId = MultiTenancy::getTenant();
				if ($tenantId)
					$data[$dbConfig['column']] = $tenantId;
				else
					$data[$dbConfig['column']] = $tableModel->columns[$dbConfig['column']]['null'] ? null : 0;
			}
		}

		return [$data, $options];
	}
}
