<?php namespace Model\MultiTenancy;

use Model\Db\AbstractDbProvider;
use Model\Db\DbConnection;

class DbProvider extends AbstractDbProvider
{
	public static function alterSelect(DbConnection $db, string $table, array $where, array $options): array
	{
		$config = MultiTenancy::getConfig();

		if (isset($config['databases'][$db->getName()]) and $config['databases'][$db->getName()]['enabled']) {
			$tableModel = $db->getTable($table);

			if (
				!($options['skip_tenancy'] ?? false)
				and isset($tableModel->columns[$config['column']])
				and !in_array($table, $config['ignore_tables'])
			) {
				$where[$config['column']] = MultiTenancy::getTenant();
			}
		}

		return [$where, $options];
	}
}
