<?php

namespace App\Migrator;

use Illuminate\Support\Facades\File;

class QueryManager
{
    const QUERIES_FILE = 'queries.json';

    /**
     * @var array $queries
     */
    private $queries;

    public function __construct()
    {
        $queriesFilesPath = base_path(self::QUERIES_FILE);

        if (!File::exists($queriesFilesPath)) {
            throw new \Exception('Arquivo de configuração queries.json não encontrado!');
        }

        $contents = File::get($queriesFilesPath);

        try {
            $data = json_decode($contents, true);

            if (is_null($data)) {
                throw new \Exception('O arquivo de configuração queries.json não está formatado corretamente!');
            }

            $this->queries = $data;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function getQueries(): array
    {
        return $this->queries['queries'];
    }
}
