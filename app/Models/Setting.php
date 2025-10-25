<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    const ELASTICSEARCH_HOST = 'elasticsearch_host';
    const ELASTICSEARCH_API_KEY = 'elasticsearch_api_key';

    const MYSQL_HOST = 'mysql_host';
    const MYSQL_PORT = 'mysql_port';
    const MYSQL_DATABASE = 'mysql_database';
    const MYSQL_USERNAME = 'mysql_username';
    const MYSQL_PASSWORD = 'mysql_password';

    const GENERAL_ROWS_PER_ITERATION = 'rows_per_iteration';
    
    protected $fillable = ['key', 'value'];
}