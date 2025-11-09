<?php

namespace App\Migrator;

use App\Models\Setting;
use Elastic\Elasticsearch\ClientBuilder;
use Log;

class Application
{
    private $elasticSearchClient;
    private $queryManager;

    private function processQueries(array $queries)
    {
        foreach ($queries as $index => $query) {
            $currentIndexQuery = ($index + 1);
            Log::channel('migrator')->info('Processing query ' . $currentIndexQuery);

            $dataSaver       = new DataSaver($this->elasticSearchClient, $query['index_name'], $query['document_identifier']);

            Log::channel('migrator')->info(json_encode($query, JSON_PRETTY_PRINT));
            $queryExecutor = new QueryExecutor(
                $dataSaver,
                $query['content']
            );

            $queryExecutor->execute();
            Log::channel('migrator')->info('Query ' . $currentIndexQuery . ' has all data migrated to index ' . $query['index_name'] . ' at ElasticSearch!');
        }
    }

    public function __invoke()
    {
        Log::channel('migrator')->info('Starting MysqlElasticMigrator');

        $this->queryManager  = new QueryManager();

        $elasticSearchHost   = Setting::where('key', Setting::ELASTICSEARCH_HOST)->first()?->value;
        $elasticSearchApiKey = Setting::where('key', Setting::ELASTICSEARCH_API_KEY)->first()?->value;

        if (empty($elasticSearchHost) || empty($elasticSearchApiKey)) {
            Log::channel('migrator')->error('Configuration Error: you must set ElasticSearch config!');

            throw new \Exception('Configuration Error: you must set ElasticSearch config!');
        }

        $this->elasticSearchClient = ClientBuilder::create()
                                        ->setHosts([$elasticSearchHost])
                                        ->setApiKey($elasticSearchApiKey)
                                        ->build();

        Log::channel('migrator')->info('ElasticSearch Connection Done!');

        Log::channel('migrator')->info('Running!');

        Log::channel('migrator')->info('Fetching queries...');
        $queries = $this->queryManager->getQueries();

        if (empty($queries)) {
            Log::channel('migrator')->info('No queries found. Go to Queries menu e create some queries. Then after that, start migration process.');   
        } else {
            Log::channel('migrator')->info('Starting processing data');
            $this->processQueries($queries);
        }

    }
}
