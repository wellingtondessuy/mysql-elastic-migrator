<?php

namespace App\Migrator;

use App\Models\Query;

class QueryManager
{
    /**
     * @var array $queries
     */
    private $queries;

    public function __construct()
    {
        $this->queries = Query::all()->toArray();
    }

    public function getQueries(): array
    {
        return $this->queries;
    }
}
