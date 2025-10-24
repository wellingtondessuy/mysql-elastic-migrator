<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    
    // Permite a atualização em massa das chaves 'key' e 'value'
    protected $fillable = ['key', 'value'];
}