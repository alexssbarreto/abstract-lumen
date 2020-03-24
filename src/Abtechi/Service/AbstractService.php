<?php

namespace Abtechi\Laravel\Service;

use Abtechi\Laravel\Repository\AbstractRepository;
use Abtechi\Laravel\Result;
use Illuminate\Http\Request;

/**
 * Class AbstractService
 * @package Abtechi\Laravel\Service
 */
abstract class AbstractService
{

    protected $repository;

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

        return new Result(true, null, $result);
    }

    /**
     * Recupera todos os registros
     * @param Request $request
     * @return Result
     */
    public function findAll(Request $request)
    {
        $pageSize = null;
        if ($request->has('page_size')) {
            $pageSize = $request->get('page_size');
        }

        $params = $request->except(['page_number', 'page_size']) ? $request->except(['page_number', 'page_size']) : ['*'];

        $result = $this->repository->findAll($params, $pageSize);

        return new Result(true, null, $result);
    }

    /**
     * Cadastra um novo registro
     * @param Request $request
     * @return Result
     */
    public function create(Request $request)
    {
        $result = $this->repository->add($request->all());

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
        $row = $this->repository->find($id);

        if (!$row) {
            return new Result(false);
        }

        $result = $this->repository->update($row, $request->all());

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
}