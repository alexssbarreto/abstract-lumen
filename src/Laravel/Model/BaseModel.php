<?php

namespace Abtechi\Laravel\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Classe base para manipulação de dados
 * Class BaseModel
 * @package Abtechi\Laravel\Model
 */
class BaseModel extends Model
{
    use SoftDeletes;
    use Uuid;

    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];
}