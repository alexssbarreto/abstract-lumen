<?php

namespace Abtechi\Laravel\Application;

use Abtechi\Laravel\Repository\AbstractRepository;
use Illuminate\Http\Request;

abstract class AbstractApplication
{
    protected $repository;

    public function __construct(AbstractRepository $repository)
    {
        $this->repository = $repository;
    }

    public function find($id)
    {
        $result = $this->repository->find($id);

        return response($result, 200);
    }

    public function findAll()
    {
        $result = $this->repository->findAll();

        return response($result, 200);
    }

    public function create(Request $request)
    {
        $result = $this->repository->add($request->all());

        if (!$result) {
            return response('', 400);
        }

        return response($result, 201);
    }

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