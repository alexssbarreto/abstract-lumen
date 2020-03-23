<?php

namespace Abtechi\Laravel\Repository;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository
{

    /** @var Model */
    public static $model = Model::class;

    public function find($id) {
        return static::$model::find($id);
    }

    public function findAll(array $params = ['*'])
    {
        return static::$model::all($params);
    }

    public function add(array $data)
    {
        $row = new static::$model;

        foreach ($data as $attribute => $value) {
            $row->{$attribute} = $value;
        }

        $row->save();
        $row->refresh();

        return $row;
    }


    public function update(Model $model, array $data)
    {
        foreach ($data as $attribute => $value) {
            $model->{$attribute} = $value;
        }

        $model->save();
        $model->refresh();

        return $model;
    }

    public function delete(Model $model)
    {
        return $model->delete();
    }

    public function deleteAll(array $params)
    {

    }
}