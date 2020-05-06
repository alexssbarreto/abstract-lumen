<?php

namespace Abtechi\Laravel\Application;

use Abtechi\Laravel\Result;
use Abtechi\Laravel\Service\AbstractService;
use Illuminate\Http\Request;

/**
 * Application
 * Class AbstractApplication
 * @package Abtechi\Laravel\Application
 */
abstract class AbstractApplication
{
    protected $paginator = true;

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
        return $this->service->find($id);
    }

    /**
     * Recupera um registro pelo uuid
     * @param $uuid
     * @return Result
     */
    public function findUuid($uuid)
    {
        return $this->service->findUuid($uuid);
    }

    /**
     * Recupera todos os registros com paginação e filtros
     * @param Request $request
     * @return Result
     */
    public function findAll(Request $request)
    {
        $params = $request->except('page_number', 'page', 'page_size');
        $pageNumber = 15;
        if ($request->has('page_number')) {
            $pageNumber = $request->input('page_number');
        }
        if ($request->has('order')) {
            if ($request->has('descending')) {
                $orderBy = json_decode($request->input('descending')) ? 'desc' : 'asc';
            } else {
                $orderBy = 'asc';
            }
            $this->orderParam = [
                $request->input('order') => $orderBy
            ];
        }
        if ($request->has('no_paginator') && $request->get('no_paginator') === 'true') {
            $this->paginator = false;
        }

        return $this->service->findAll($params, $this->orderParam, $this->paginator, $pageNumber);
    }

    /**
     * Cadastra um novo registro
     * @param Request $request
     * @return Result
     */
    public function create(Request $request)
    {
        return $this->service->create($request->route('uuid'), $request->post());
    }

    /***
     * Atualiza um registro
     * @param $uuid
     * @param Request $request
     * @return Result
     */
    public function update($uuid, Request $request)
    {
        return $this->service->update($uuid, $request->post());
    }

    /**
     * Visualiza um registro
     * @param $uuid
     * @param Request $request
     * @return Result
     */
    public function visualizar($uuid, Request $request)
    {
        return $this->service->findUuid($uuid);
    }

    /**
     * Exclui um registro
     * @param $uuid
     * @param Request $request
     * @return Result
     */
    public function delete($uuid, Request $request)
    {
        return $this->service->delete($uuid, $request->all());
    }

    /**
     * Recupera estrutura de opções: chave => valor
     * @param Request $request
     * @return Result
     */
    public function listOptions(Request $request)
    {
        return $this->service->listarOptions($request->all(), $this->optionsParam, $this->orderParam);
    }
}