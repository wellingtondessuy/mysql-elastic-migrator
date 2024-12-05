<?php

namespace App\Migrator;

use App\DatabaseInsertions\LoadDatabase;
use Elastic\Elasticsearch\ClientBuilder;
use Log;

class Application
{
    private $elasticSearchClient;
    private $queryManager;

    public function __construct() {
        Log::info('Starting MysqlElasticMigrator');

        $this->queryManager  = new QueryManager();

        $elasticSearchHost   = env('ELASTICSEARCH_HOST');
        $elasticSearchApiKey = env('ELASTICSEARCH_API_KEY');

        if (empty($elasticSearchHost) || empty($elasticSearchApiKey)) {
            Log::error('Configuration Error: you must set ElasticSearch config data in .env file!');

            throw new \Exception('Configuration Error: you must set ElasticSearch config data in .env file!');
        }

        $this->elasticSearchClient = ClientBuilder::create()
                                        ->setHosts([$elasticSearchHost])
                                        ->setApiKey($elasticSearchApiKey)
                                        ->build();

        Log::info('ElasticSearch Connection Done!');
    }

    private function processQueries(array $queries)
    {
        foreach ($queries as $index => $query) {
            $currentIndexQuery = ($index + 1);
            Log::info('Processing query ' . $currentIndexQuery);

            $dataSaver       = new DataSaver($this->elasticSearchClient, $query['index'], $query['document_identifier']);

            Log::info(json_encode($query, JSON_PRETTY_PRINT));
            $queryExecutor = new QueryExecutor(
                $dataSaver,
                $query['query']
            );

            $queryExecutor->execute();
            Log::info('Query ' . $currentIndexQuery . ' has all data migrated to index ' . $query['index'] . ' at ElasticSearch!');
        }
    }

    public function __invoke()
    {
        Log::info('Running!');

        Log::info('Loading queries.json');
        $queries = $this->queryManager->getQueries();

        Log::info('Starting processing data');
        $this->processQueries($queries);
    }
}
