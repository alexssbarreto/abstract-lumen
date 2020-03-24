<?php

namespace Abtechi\Laravel\Application;

use Abtechi\Laravel\Service\AbstractService;
use Illuminate\Http\Request;

/**
 * Application
 * Class AbstractApplication
 * @package Abtechi\Laravel\Application
 */
abstract class AbstractApplication
{
    protected $service;

    /**
     * AbstractApplication constructor.
     * @param $service
     */
    public function __construct(AbstractService $service)
    {
        $this->service = $service;
    }

    /**
     * Recupera um registro
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        $result = $this->service->find($id);

        return response($result->getData(), 200);
    }

    /**
     * Recupera todos os registros
     * @return mixed
     */
    public function findAll(Request $request)
    {
        $result = $this->service->findAll($request);

        return response($result->getData(), 200);
    }

    /**
     * Cadastra um novo registro
     * @param Request $request
     * @return mixed
     */
    public function create(Request $request)
    {
        $result = $this->service->create($request);

        if (!$result->isResult()) {
            return response('', 400);
        }

        return response($result->getData(), 201);
    }

    /**
     * Atualiza um registro
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        $result = $this->service->update($id, $request);

        if (!$result->isResult() && !$result->getMessage()) {
            return response('', 404);
        }

        if (!$result->isResult() && $result->getMessage()) {
            return response('', 400);
        }

        return response($result->getData(), 204);
    }

    /**
     * Deleta uma registro
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $result = $this->service->delete($id);

        if (!$result->isResult() && !$result->getMessage()) {
            return response('', 404);
        }

        if (!$result->isResult() && $result->getMessage()) {
            return response('', 400);
        }

        return response($result, 204);
    }
}