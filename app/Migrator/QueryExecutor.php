<?php

namespace App\Migrator;

use Illuminate\Support\Facades\DB;
use Log;
use PDO;

class QueryExecutor
{
    private $dataSaver;
    private $query;
    private $rowPerIteration = 10000;

    public function __construct(
        DataSaver $dataSaver,
        string $query
    ) {
        $this->dataSaver          = $dataSaver;
        $this->query              = $query . ' LIMIT :limit OFFSET :offset';
        $this->rowPerIteration    = env('ROW_PER_ITERATION', 10000);
    }

    public function execute()
    {
        $pdo = DB::getPdo();

        $iteration = 0;

        do {
            Log::info('Executing query! Iteration: ' . $iteration);
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

        Log::info('All data migrated to ElasticSearch');
    }
}
