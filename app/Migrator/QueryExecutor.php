<?php

namespace App\Migrator;

use App\Models\Setting;
use Config;
use Illuminate\Support\Facades\DB;
use Log;
use PDO;

class QueryExecutor
{
    const CONFIG_FROM_MYSQL_DATABASE = 'database.connections.from_mysql_database';

    private $dataSaver;
    private $query;
    private $rowPerIteration = 10000;

    public function __construct(
        DataSaver $dataSaver,
        string $query
    ) {
        $this->dataSaver          = $dataSaver;
        $this->query              = $query . ' LIMIT :limit OFFSET :offset';

        $rowsPerIteration = Setting::where('key', Setting::GENERAL_ROWS_PER_ITERATION)->first()?->value;

        $this->rowPerIteration    = $rowsPerIteration?? 10000;

        $mysqlHost     = Setting::where('key', Setting::MYSQL_HOST)->first()?->value;
        $mysqlPort     = Setting::where('key', Setting::MYSQL_PORT)->first()?->value;
        $mysqlDatabase = Setting::where('key', Setting::MYSQL_DATABASE)->first()?->value;
        $mysqlUsername = Setting::where('key', Setting::MYSQL_USERNAME)->first()?->value;
        $mysqlPassword = Setting::where('key', Setting::MYSQL_PASSWORD)->first()?->value;
        
        $fromMysqlDatabaseConfig = [
            'driver'    => 'mysql',
            'host'      => $mysqlHost,
            'port'      => $mysqlPort,
            'database'  => $mysqlDatabase,
            'username'  => $mysqlUsername,
            'password'  => $mysqlPassword,
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null,
        ];

        Config::set(self::CONFIG_FROM_MYSQL_DATABASE, $fromMysqlDatabaseConfig);
    }

    public function execute()
    {
        $pdo = DB::connection(self::CONFIG_FROM_MYSQL_DATABASE)->getPdo();

        $iteration = 0;

        do {
            Log::channel('migrator')->info('Executing query! Iteration: ' . $iteration);
            $stmt = $pdo->prepare($this->query);
            $stmt->bindValue(':limit', $this->rowPerIteration, PDO::PARAM_INT);

            $offset = $iteration * $this->rowPerIteration;
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($rows)) {
                break;
            }

            $this->dataSaver->save($rows);

            $iteration += 1;
        } while (!empty($rows));

        Log::channel('migrator')->info('All data migrated to ElasticSearch');
    }
}
