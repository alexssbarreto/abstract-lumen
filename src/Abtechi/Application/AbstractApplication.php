<?php

namespace Abtechi\Laravel\Application;

use Abtechi\Laravel\Repository\AbstractRepository;
use Illuminate\Http\Request;

abstract class AbstractApplication
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
     * @return mixed
     */
    public function find($id)
    {
        $result = $this->repository->find($id);

        return response($result, 200);
    }

    /**
     * Recupera todos os registros
     * @return mixed
     */
    public function findAll(Request $request)
    {
        $result = $this->repository->findAll();

        return response($result, 200);
    }

    /**
     * Cadastra um novo registro
     * @param Request $request
     * @return mixed
     */
    public function create(Request $request)
    {
        $result = $this->repository->add($request->all());

        if (!$result) {
            return response('', 400);
        }

        return response($result, 201);
    }

    /**
     * Atualiza um registro
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        $row = $this->repository->find($id);

        if (!$row) {
            return response('', 404);
        }

        $result = $this->repository->update($row, $request->all());

        if (!$result) {
            return response('', 400);
        }

        return response($result, 204);
    }

    /**
     * Deleta uma registro
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $row = $this->repository->find($id);

        if (!$row) {
            return response('', 404);
        }

        $result = $this->repository->delete($row);

        if (!$result) {
            return response('', 400);
        }

        return response('', 204);
    }
}