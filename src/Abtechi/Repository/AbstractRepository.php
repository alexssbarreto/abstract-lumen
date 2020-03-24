<?php

namespace Abtechi\Laravel\Repository;

use Abtechi\Laravel\Validators\AbstractValidator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Classe base para manipulação dos dados utilizando
 * o Eloquente
 * Class AbstractRepository
 * @package Abtechi\Laravel\Repository
 */
abstract class AbstractRepository
{

    protected $pageSizeMax = 200;

    protected $page = 1;

    /** @var Model */
    public static $model = Model::class;
    public static $validator = AbstractValidator::class;

    private $describesText = [
        'varchar',
        'text',
        'longtext',
        'mediumtext',
        'tinytext'
    ];

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
    public function findAll(array $params = ['*'], $pageSize = 15)
    {
        if ($pageSize && $pageSize > $this->pageSizeMax) {
            $pageSize = $this->pageSizeMax;
        }

        /** @var Model $model */
        $model = new static::$model;

        $describe = DB::select('describe ' . $model->getTable());

        $querySelect = clone $model;

        foreach ($params as $key => $value) {
            if (Schema::hasColumn($model->getTable(), $key)) {
                $column = array_filter($describe, function($column) use($key, $value, &$querySelect){
                    if ($column->Field == $key) {
                        if (in_array(strtolower(explode('(', $column->Type)[0]), $this->describesText)) {
                            $querySelect = $querySelect->where($key, 'LIKE', '%' . $value . '%');

                            return $column;
                        }
                    }
                });

                if ($column) {
                    continue;
                }

                $querySelect = $querySelect->where($key, $value);
            }
        }

        return $querySelect->simplePaginate($pageSize);
    }

    /**
     * Cadastra um novo registro
     * @param array $data
     * @return mixed
     */
    public function add(array $data)
    {
        /** @var Model $row */
        $row = new static::$model;

        $row = $this->prepareStatementAttr($row, $data);

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
        $model = $this->prepareStatementAttr($model, $data);

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

    private function prepareStatementAttr(Model $model, array $data)
    {
        foreach ($data as $attribute => $value) {
            if (in_array($attribute, static::$validator::$attributes)) {
                $model->{$attribute} = $value;
            }
        }

        return $model;
    }
}