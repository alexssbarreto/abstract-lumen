<?php

namespace Abtechi\Laravel\Repository;

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
    public function find($id)
    {
        return static::$model::find($id);
    }

    /**
     * Recupera um registro pelo uuid
     * @param $uuid
     * @return mixed
     */
    public function findUuid($uuid)
    {
        return static::$model::firstWhere('uuid', $uuid);
    }

    /**
     * Retorna o model modelo
     * @return Model
     */
    public function getModel()
    {
        return static::$model;
    }

    /**
     * Cadastra um novo registro
     * @param Model $model
     * @return Model
     */
    public function add(Model $model, array $data = [])
    {
        $model->save();
        $model->refresh();

        return $model;
    }

    /**
     * Atualiza um registro
     * @param Model $model
     * @return Model
     */
    public function update(Model $model, array $data = [])
    {
        $model->save();
        $model->refresh();

        return $model;
    }

    /**
     * Recupera os registros aplicando paginação e ordenação
     * @param array $params
     * @param int $pageSize
     * @param bool $pagination
     * @param array $order
     * @return mixed
     * @throws \Exception
     */
    public function findAll(array $params = [], array $order = [], $pagination = true, $pageSize = 15)
    {
        if ($pageSize && $pageSize > $this->pageSizeMax) {
            $pageSize = $this->pageSizeMax;
        }

        /** @var Model $model */
        $model = new static::$model;

        $describe = DB::select('describe ' . $model->getTable());

        $querySelect = clone $model;

        if ($params) {
            foreach ($params as $key => $value) {
                if (Schema::hasColumn($model->getTable(), $key)) {
                    if ($this->hasAttributeDescribe($key, $describe, true)) {
                        $querySelect = $querySelect->where($key, 'LIKE', '%' . $value . '%');

                        continue;
                    }

                    $querySelect = $querySelect->where($key, $value);
                }
            }
        }

        if ($order) {
            foreach ($order as $key => $option) {
                if (!$this->hasAttributeDescribe($key, $describe)) {
                    throw new \Exception(sprintf('Atibuto de ordenação não existe: %s', $key));
                }

                $querySelect = $querySelect->orderBy($key, $option);
            }
        }

        if ($pagination) {
            return $querySelect->simplePaginate($pageSize);
        }

        return $querySelect->get();
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

    /**
     * Checa se attibute possui nas descrições da tabela
     * @param $attribute
     * @param array $describe
     * @return bool
     */
    private function hasAttributeDescribe($attribute, array $describe, $verifyIsText = false)
    {
        $hasAttribute = array_filter($describe, function($column) use($attribute, $verifyIsText){
            if ($attribute == $column->Field) {
                if (!$verifyIsText) {
                    return true;
                }

                if (in_array(strtolower(explode('(', $column->Type)[0]), $this->describesText)) {
                    return true;
                }

                return false;
            }
        });

        if ($hasAttribute) {
            return true;
        }

        return false;
    }
}