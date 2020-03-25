<?php

namespace Abtechi\Laravel\Service;

use Abtechi\Laravel\Repository\AbstractRepository;
use Abtechi\Laravel\Result;
use Abtechi\Laravel\Validators\AbstractValidator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class AbstractService
 * @package Abtechi\Laravel\Service
 */
abstract class AbstractService
{

    /**
     * Estrutura de dados para options
     * @var array
     */
    protected $optionsParam = [
        'option' => 'id',
        'value' => 'id'
    ];

    /**
     * Ordenação nan listagem de conteúdo
     * @var array
     */
    protected $orderParam = [
        'id' => 'DESC'
    ];

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

        if ($result->count()) {
            return new Result(true, null, $result);
        }

        return new Result(false);
    }

    /**
     * Recupera todos os registros
     * @param Request $request
     * @return Result
     */
    public function findAll(Request $request, $pagination = true)
    {
        $pageSize = null;
        if ($request->has('page_size')) {
            $pageSize = $request->get('page_size');
        }

        $params = $request->except(['page_number', 'page_size']) ? $request->except(['page_number', 'page_size']) : ['*'];

        $result = $this->repository->findAll($params, $pageSize, $pagination, $this->orderParam);

        return new Result(true, null, $result);
    }

    /**
     * Cadastra um novo registro
     * @param Request $request
     * @return Result
     */
    public function create(Request $request)
    {
        $validate = $this->validateCreate($request);

        if (!$validate->isResult()) {
            return $validate;
        }

        $row = new $this->repository::$model;
        $row = $this->prepareStatementAttr($row, $request->post());

        $result = $this->repository->add($row);

        if (!$result) {
            return new Result(false);
        }

        return new Result(true, null, $result);
    }

    /**
     * Atualiza um registro
     * @param $id
     * @param Request $request
     * @return Result
     */
    public function update($id, Request $request)
    {
        $validate = $this->validateUpdate($request);

        if (!$validate->isResult()) {
            return $validate;
        }

        $row = $this->repository->find($id);

        if (!$row) {
            return new Result(false);
        }

        $row = $this->prepareStatementAttr($row, $request->post());

        $result = $this->repository->update($row);

        if (!$result) {
            return new Result(false, 'Não foi possível atualizar o registro.');
        }

        return new Result(true, null, $result);
    }

    /**
     * Deleta uma registro
     * @param $id
     * @return Result
     */
    public function delete($id)
    {
        $row = $this->repository->find($id);

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
     * @param Request $request
     * @return Result
     */
    public function validateCreate(Request &$request)
    {
        return new Result(true);
    }

    /**
     * Realiza validações para atualização de dados
     * @param Request $request
     * @return Result
     */
    public function validateUpdate(Request &$request)
    {
        return $this->validateCreate($request);
    }

    /**
     * Prepara estrutura de dados para opções: chave => valor
     * @param Request $request
     * @return Result
     * @throws \Exception
     */
    public function listarOptions(Request $request)
    {
        $result = $this->findAll($request, false);

        if (!$result->isResult()) {
            return new Result(true, null, []);
        }

        if (!is_array($this->optionsParam) || count($this->optionsParam) !== 2) {
            throw new \Exception('Obrigatório que a configuração dos parâmetros dos options seja um array [option => option, value => value]');
        }

        $aResult = [];

        /** @var Model $row */
        foreach ($result->getData() as $row) {
            if (!$row->getAttribute($this->optionsParam['value'])) {
                throw new \Exception('O value option não existe no $optionsParam["value"]');
            }

            if (!$row->getAttribute($this->optionsParam['option'])) {
                throw new \Exception('O value option não existe no $optionsParam["option"]');
            }

            $aResult[] = [
                'value'  => $row->{$this->optionsParam['value']},
                'option' => $row->{$this->optionsParam['option']}
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
    private function prepareStatementAttr(Model $model, array $data)
    {
        foreach ($data as $attribute => $value) {
            if (in_array($attribute, static::$validator::$attributes)) {
                $model->{$attribute} = trim($value);
            }
        }

        return $model;
    }
}