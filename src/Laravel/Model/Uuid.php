<?php

namespace Abtechi\Laravel\Model;

use Ramsey\Uuid\Uuid as Guid;

/**
 * Gera uuid para as entidades do modelo
 * Trait Uuid
 * @package App\Model
 */
trait Uuid
{
    public static function bootUuid()
    {
        static::creating(function ($model) {
            $model->uuid = Guid::uuid4();
        });
    }
}