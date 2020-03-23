<?php

namespace Abtechi\Laravel\Controller;

use Abtechi\Laravel\Validators\AbstractValidator;
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

    public function listar(Request $request)
    {
        return $this->application->findAll();
    }

    public function incluir(Request $request)
    {
        $this->validate($request, $this->validator::$rules, $this->validator::$messages);

        return $this->application->create($request);
    }

    public function editar($id, Request $request)
    {
        $this->validate($request, $this->validator::$rules, $this->validator::$messages);

        $response = $this->application->update($id, $request);

        if (!$response) {
            return response('', 404);
        }

        return response(null, 204);
    }

    public function excluir($id, Request $request)
    {
        return $this->application->delete($id);
    }
}