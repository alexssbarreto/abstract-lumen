<?php

namespace Abtechi\Laravel\Controller;

use Abtechi\Laravel\Application\AbstractApplication;
use Abtechi\Laravel\Validators\AbstractValidator;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;

class AbstractController extends Controller
{


    protected $application;

    protected $validator = AbstractValidator::class;

    /**
     * AbstractController constructor.
     * @param $application
     */
    public function __construct(AbstractApplication $application)
    {
        $this->application = $application;

        if (!$this->validator) {
            throw new \InvalidArgumentException('Obrigatório informar o Validator da requisição');
        }
    }

    /**
     * Lista um ou todos os registros
     * @param null $id
     * @param Request $request
     * @return mixed
     */
    public function listar($id = null, Request $request)
    {
        if ($id) {
            return $this->application->find($id);
        }

        return $this->application->findAll($request);
    }

    /**
     * Inclui novo registro
     * @param Request $request
     * @return mixed
     */
    public function incluir(Request $request)
    {
        $this->validate($request, $this->validator::$rules, $this->validator::$messages);

        return $this->application->create($request);
    }

    /**
     * Editar um registro
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function editar($id, Request $request)
    {
        $this->validate($request, $this->validator::$rules, $this->validator::$messages);

        return $this->application->update($id, $request);
    }

    /**
     * Exclui um registro
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function excluir($id, Request $request)
    {
        return $this->application->delete($id);
    }

    /**
     * Recupera a listagem em formato de options json: [chave => valor]
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function listarOptions(Request $request)
    {
        return $this->application->listOptions($request);
    }
}