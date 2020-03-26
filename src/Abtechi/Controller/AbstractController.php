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
     * @param null $uuid
     * @param Request $request
     * @return mixed
     */
    public function listar($uuid = null, Request $request)
    {
        if ($uuid) {
            $result = $this->application->findUuid($uuid);
        } else {
            $result = $this->application->findAll($request);
        }

        if ($result->isResult()) {
            return response($result->getData(), 200);
        }

        return response($result->getData(), 404);
    }

    public function visualizar($uuid = null, Request $request)
    {
        die('oi');

        echo '<pre>';
        var_dump(func_num_args());
        var_dump($request->all());
        var_dump($request->get); die('aqui');

        if ($uuid) {
            return $this->application->findUuid($uuid);
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

        $result = $this->application->create($request);

        if (!$result->isResult() && !$result->getMessage()) {
            return response('', 404);
        }

        if (!$result->isResult() && $result->getMessage()) {
            return response()->json((array)$result, 400);
        }

        if ($result->getData()) {
            return response()->json($result->getData(), 201);
        }

        return response(null, 204);
    }

    /**
     * Editar um registro
     * @param $uuid
     * @param Request $request
     * @return mixed
     */
    public function editar($uuid, Request $request)
    {
        $this->validate($request, $this->validator::$rules, $this->validator::$messages);

        $result = $this->application->update($uuid, $request);

        if (!$result->isResult() && !$result->getMessage()) {
            return response('', 404);
        }

        if (!$result->isResult() && $result->getMessage()) {
            return response()->json((array)$result, 400);
        }

        if ($result->getData()) {
            return response()->json($result->getData(), 200);
        }

        return response(null, 204);
    }

    /**
     * Exclui um registro
     * @param $uuid
     * @param Request $request
     * @return mixed
     */
    public function excluir($uuid, Request $request)
    {
        $result = $this->application->delete($uuid);

        if (!$result->isResult() && !$result->getMessage()) {
            return response('', 404);
        }

        if (!$result->isResult() && $result->getMessage()) {
            return response()->json((array)$result, 400);
        }

        return response()->json($result->getData(), 204);
    }

    /**
     * Recupera a listagem em formato de options json: [chave => valor]
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function listarOptions(Request $request)
    {
        $result = $this->application->listOptions($request);

        return response()->json($result->getData());
    }
}