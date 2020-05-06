<?php

namespace Abtechi\Laravel\Service;

use Abtechi\Laravel\Repository\AbstractRepository;
use Abtechi\Laravel\Result;
use Abtechi\Laravel\Validators\AbstractValidator;
use Abtechi\Middleware\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class AbstractService
 * @package Abtechi\Laravel\Service
 */
abstract class AbstractService
{

    /**
     * Acesso ao serviço Repository
     * @var AbstractRepository
     */
    protected $repository;

    /**
     * Validações essenciais para create and update
     * @var string
     */
    public static $validator = AbstractValidator::class;

    /**
     * Instância de acesso ao banco de dados
     * AbstractApplication constructor.
     * @param AbstractRepository $repository
     */
    public function __construct(AbstractRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Recupera um registro
     * @param $id
     * @return Result
     */
    public function find($id)
    {
        $result = $this->repository->find($id);

        if ($result) {
            return new Result(true, null, $result);
        }

        return new Result(false);
    }

    /**
     * Recupera um registro pelo uuid
     * @param $uuid
     * @return Result
     */
    public function findUuid($uuid)
    {
        $result = $this->repository->findUuid($uuid);

        if ($result) {
            return new Result(true, null, $result);
        }

        return new Result(false);
    }

    /**
     * Recupera todos os registros
     *
     * @param array $params
     * @param array $order
     * @param int $pageSize
     * @param bool $pagination
     * @return Result
     */
    public function findAll(array $params, array $order = [], $pagination = true, $pageSize = 15)
    {
        $params = $this->prepareStatementSearch($params);

        if (key_exists(key($order), static::$validator::$attributes)) {
            $orderBy[static::$validator::$attributes[key($order)]] = $order[key($order)];
        } else {
            $orderBy = [];
        }

        $result = $this->repository->findAll($params, $orderBy, $pagination, $pageSize);

        return new Result(true, null, $result);
    }

    /**
     * Cadastra um novo registro
     * @param array $data
     * @return Result
     */
    public function create($uuid = null, array $data)
    {
        $validate = $this->validateCreate($data);

        if (!$validate->isResult()) {
            return $validate;
        }

        $row = $this->repository->getModel();
        $row = new $row();

        $row = $this->prepareStatementAttr($row, $data);

        $result = $this->repository->add($row, $data);

        if ($result) {
            return new Result(true, null, $result);
        }

        return new Result(false);
    }

    /***
     * Atualiza um registro
     * @param $uuid
     * @param array $data
     * @return Result
     */
    public function update($uuid, array $data)
    {
        $validate = $this->validateUpdate($data);

        if (!$validate->isResult()) {
            return $validate;
        }

        /** @var Model $row */
        $row = $this->repository->findUuid($uuid);

        if (!$row) {
            return new Result(false);
        }

        $row = $this->prepareStatementAttr($row, $data);

        $result = $this->repository->update($row, $data);

        if ($result) {
            return new Result(true, null, $result);
        }

        return new Result(false, 'Não foi possível atualizar o registro.');
    }

    /**
     * Deleta uma registro
     * @param $uuid
     * @param array $data
     * @return Result
     */
    public function delete($uuid, array $data = [])
    {
        $row = $this->repository->findUuid($uuid);

        if (!$row) {
            return new Result(false);
        }

        $result = $this->repository->delete($row);

        if (!$result) {
            return new Result(false, 'Não foi possível excluir o registro');
        }

        return new Result(true);
    }

    /**
     * Realiza validações para criação de dados
     * @param array $data
     * @return Result
     */
    public function validateCreate(array &$data)
    {
        return new Result(true);
    }

    /**
     * Realiza validações para atualização de dados
     * @param array $data
     * @return Result
     */
    public function validateUpdate(array &$data)
    {
        return $this->validateCreate($data);
    }

    /***
     * Prepara estrutura de dados para opções: chave => valor
     * @param array $params
     * @param array $options
     * @param array $order
     * @return Result
     * @throws \Exception
     */
    public function listarOptions(array $params, array $options, array $order = [])
    {
        $result = $this->findAll($params, $order, false);

        if (!$result->isResult()) {
            return new Result(true, null, []);
        }

        if (count($options) !== 2) {
            throw new \Exception('Obrigatório que a configuração dos parâmetros dos options seja um array [option => option, value => value]');
        }

        $aResult = [];

        /** @var Model $row */
        foreach ($result->getData()['data'] as $row) {
            if (!$row->getAttribute($options['value'])) {
                throw new \Exception('O value option não existe no $optionsParam["value"]');
            }

            if (!$row->getAttribute($options['option'])) {
                throw new \Exception('O value option não existe no $optionsParam["option"]');
            }

            $aResult[] = [
                'value'  => $row->{$options['value']},
                'label' => $row->{$options['option']}
            ];
        }

        return new Result(true, null, $aResult);
    }

    /**
     * Prepara estrutura do modelo de dados
     * @param Model $model
     * @param array $data
     * @return Model
     */
    protected function prepareStatementAttr(Model $model, array $data)
    {
        $attributes = static::$validator::$attributes;

        foreach ($data as $attribute => $value) {
            if (key_exists($attribute, $attributes)) {
                $attributeModel = $attributes[$attribute] ? $attributes[$attribute] : $attribute;

                if (is_string($value)) {
                    $model->{$attributeModel} = trim($value);
                } else {
                    $model->{$attributeModel} = $value;
                }
            }
        }

        return $model;
    }

    /**
     * Prepara estrutura de dados para pesquisa de dados
     * @param array $data
     * @return array
     */
    protected function prepareStatementSearch(array $data)
    {
        $attributes = static::$validator::$attributes;
        $aSearch = [];

        foreach ($data as $attribute => $value) {
            if (key_exists($attribute, $attributes)) {
                $attributeSearch = $attributes[$attribute] ? $attributes[$attribute] : $attribute;

                if (is_string($value)) {
                    $aSearch[$attributeSearch] = trim($value);
                } else {
                    $aSearch[$attributeSearch] = $value;
                }
            }
        }

        return $aSearch;
    }
}