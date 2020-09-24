<?php

namespace Abtechi\Laravel\Repository;

use Abtechi\Laravel\Result;
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

    protected $withSelect = false;

    /** @var Model */
    public static $model = Model::class;

    private $describeModel;
    private $objectModel;
    protected $querySelect;

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
        $model = static::$model;

        if ($this->withSelect) {
            return $model::with($this->withSelect)->find($id);
        }

        return $model::find($id);
    }

    /**
     * Recupera um registro pelo uuid
     * @param $uuid
     * @return mixed
     */
    public function findUuid($uuid)
    {
        $model = static::$model;

        if ($this->withSelect) {
            return $model::with($this->withSelect)->firstWhere('uuid', $uuid);
        }

        return $model::firstWhere('uuid', $uuid);
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
     * Adiciona novo registro
     * @param Model $model
     * @param array $data
     * @return Result
     */
    public function add(Model $model, array $data = [])
    {
        $model->save();
        $model->refresh();

        return new Result(true, null, $model);
    }

    /**
     * Atualiza registro
     * @param Model $model
     * @param array $data
     * @return Result
     */
    public function update(Model $model, array $data = [])
    {
        $model->save();
        $model->refresh();

        return new Result(true, null, $model);
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
        $this->objectModel = new static::$model;
        $this->describeModel();
        $this->querySelect = clone $this->objectModel;

        $queryRequest = $this->querySelect($params);

        if (!$queryRequest->isResult()) {
            return $queryRequest;
        }

        $this->querySelect = $queryRequest->getData();

        if ($order) {
            $this->querySelect = $this->orderBy($order);
        }

        if ($pagination) {
            if ($this->withSelect) {
                return new Result(true, null, $this->querySelect->with($this->withSelect)->paginate($pageSize));
            }
            return new Result(true, null, $this->querySelect->paginate($pageSize));
        }

        if ($this->withSelect) {
            return new Result(true, null, [
                'data' => $this->querySelect->with($this->withSelect)->get()
            ]);
        }

        return new Result(true, null, [
            'data' => $this->querySelect->get()
        ]);
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
        $hasAttribute = array_filter($describe, function ($column) use ($attribute, $verifyIsText) {
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

    /**
     * Descreve os attributes do Model
     * @return array
     */
    protected function describeModel()
    {
        $this->describeModel = DB::select('describe ' . $this->objectModel->getTable());
    }

    /**
     * Prepara estrutura de dados para query
     * @param array $params
     * @return Result
     */
    protected function querySelect(array $params)
    {
        $preQuery = $this->preQuerySelect($params);

        if (!$preQuery->isResult()) {
            return $preQuery;
        }

        $this->querySelect = $preQuery->getData();

        foreach ($params as $key => $value) {
            if (Schema::hasColumn($this->objectModel->getTable(), $key) && is_string($value)) {
                if ($this->hasAttributeDescribe($key, $this->describeModel, true)) {
                    $this->querySelect = $this->querySelect->where($key, 'LIKE', '%' . $value . '%');

                    continue;
                }

                $this->querySelect = $this->querySelect->where($key, $value);
            }
        }

        return new Result(true, null, $this->querySelect);
    }

    /**
     * Pré-processa querySelect
     * @param array $params
     * @return Result
     */
    protected function preQuerySelect(array $params)
    {
        return new Result(true, null, $this->querySelect);
    }

    /**
     * Ordena resultado da listagem
     * @param array $order
     * @return mixed
     * @throws \Exception
     */
    protected function orderBy(array $order)
    {
        $querySelect = $this->querySelect;

        foreach ($order as $key => $option) {
            if (!$this->hasAttributeDescribe($key, $this->describeModel)) {
                throw new \Exception(sprintf('Atibuto de ordenação não existe: %s', $key));
            }

            $querySelect = $querySelect->orderBy($key, $option);
        }

        return $querySelect;
    }

    /**
     * @return mixed|Model
     */
    protected function getQuerySelect()
    {
        return $this->querySelect;
    }
}