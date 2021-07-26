<?php

namespace Abtechi\Laravel\Controller;

use Abtechi\Laravel\Application\AbstractApplication;
use Abtechi\Laravel\Validators\AbstractValidator;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;

class AbstractControllerApi extends Controller
{

    protected $application;

    protected $validator = AbstractValidator::class;

    /**
     * AbstractControllerApi constructor.
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
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function listar(Request $request, $uuid = null)
    {
        $request->merge([
            'uuid' => $uuid
        ]);

        $result = $this->application->findAll($request);

        if ($result->isResult()) {
            return response()->json($result->getData(), 200);
        }

        return response()->json($result, 400);
    }

    /**
     * Visualizar um determinado registro
     * @param null $uuid
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function visualizar(Request $request, $uuid = null)
    {
        $result = $this->application->visualizar($uuid, $request);

        if ($result->isResult()) {
            return response()->json($result->getData(), 200);
        }

        return response()->json($result->getData(), 404);
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

        if (!$result->isResult()) {
            return response()->json((array)$result, 400);
        }

        if ($result->getData()) {
            return response()->json($result->getData(), 201);
        }

        return response()->json(null, 204);
    }

    /**
     * Editar um registro
     * @param $uuid
     * @param Request $request
     * @return mixed
     */
    public function editar(Request $request, $uuid)
    {
        $this->validate($request, $this->validator::$rules, $this->validator::$messages);

        $result = $this->application->update($uuid, $request);

        if (!$result->isResult()) {
            return response()->json((array)$result, 400);
        }

        if ($result->getData()) {
            return response()->json($result->getData(), 201);
        }

        return response()->json(null, 204);
    }

    /**
     * Exclui um registro
     * @param $uuid
     * @param Request $request
     * @return mixed
     */
    public function excluir(Request $request, $uuid)
    {
        $result = $this->application->delete($uuid, $request);

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

        if (!$result->isResult()) {
            return response()->json((array)$result, 400);
        }

        if ($result->getData()) {
            return response()->json($result->getData(), 200);
        }

        return response()->json(null, 200);
    }
}