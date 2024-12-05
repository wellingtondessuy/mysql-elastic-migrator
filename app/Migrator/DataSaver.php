<?php

namespace App\Migrator;

use Elastic\Elasticsearch\Client;
use Log;

class DataSaver
{
    private $elastic;
    private $indexName;
    private $documentIdentifier;

    public function __construct(Client $elastic, string $indexName, string $documentIdentifier = null)
    {
        $this->elastic            = $elastic;
        $this->indexName          = $indexName;
        $this->documentIdentifier = $documentIdentifier;

        $this->createIndex();
    }

    private function createIndex()
    {
        $params = [
            'index' => $this->indexName
        ];

        $response = $this->elastic->indices()->exists($params);

        $indexExists = $response->getStatusCode() != 404;

        if ($indexExists) {
            Log::info('ElasticSearch Index Already Exists: ' . $this->indexName);

            return;
        }

        $response = $this->elastic->indices()->create($params);

        Log::info('ElasticSearch Index Created: ' . $this->indexName);
    }

    public function save(array $data)
    {
        $params = ['body' => []];

        Log::info('Documents to save: ' . sizeof($data));

        foreach ($data as $row) {
            $documentData = [
                'index' => [
                    '_index' => $this->indexName,
                ]
            ];

            if (!is_null($this->documentIdentifier)) {
                $documentData['index']['_id'] = $row[$this->documentIdentifier];
            }

            $params['body'][] = $documentData;

            $params['body'][] = $row;
        }

        Log::info('ElasticSearch Saving Documents... ');
        $this->elastic->bulk($params);
    }
}
