<?php

namespace Abtechi\Laravel\Repository;

use Illuminate\Database\Eloquent\Model;

/**
 * Classe base para manipulação dos dados utilizando
 * o Eloquente
 * Class AbstractRepository
 * @package Abtechi\Laravel\Repository
 */
abstract class AbstractRepository
{

    /** @var Model */
    public static $model = Model::class;

    /**
     * Recupera um registro
     * @param $id
     * @return mixed
     */
    public function find($id) {
        return static::$model::find($id);
    }

    /**
     * Recupera todos os registros
     * @param array $params
     * @return mixed
     */
    public function findAll(array $params = ['*'])
    {
        return static::$model::all($params);
    }

    /**
     * Cadastra um novo registro
     * @param array $data
     * @return mixed
     */
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

    /**
     * Atualiza um registro
     * @param Model $model
     * @param array $data
     * @return Model
     */
    public function update(Model $model, array $data)
    {
        foreach ($data as $attribute => $value) {
            $model->{$attribute} = $value;
        }

        $model->save();
        $model->refresh();

        return $model;
    }

    /**
     * Remove um registro
     * @param Model $model
     * @return mixed
     */
    public function delete(Model $model)
    {
        return $model->delete();
    }
}