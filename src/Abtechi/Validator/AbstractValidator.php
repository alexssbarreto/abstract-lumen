<?php

namespace Abtechi\Laravel\Validators;

/**
 * Classe base para realização das validações da Request
 * Class AbstractValidator
 * @package Abtechi\Laravel\Validators
 */
abstract class AbstractValidator
{

    /**
     * Atributes de dados
     * @var array
     */
    public static $attributes = [];

    /**
     * Validators dos attibutes
     * @var array
     */
    public static $rules = [];

    public static $messages = [
        'required' => 'Atributo é obrigatório.',
        'email' => 'E-mail inválido.',
        'string' => 'Formato aceito: apenas String.',
        'integer' => 'Formato aceito: apenas Inteiro.',
        'int' => 'Formato aceito: apenas Inteiro.',
        'boolean' => 'Formato aceito: apenas boleano (true or false).',
        'uuid' => 'Formato aceito: apenas UUID',
        'date' => 'Formato de data inválido: yyyy-mm-dd',
        'confirmed' => 'Dados :attribute não são iguais',
        'min' => 'Mínimo de caracteres :min',
        'max' => 'Máximo de caracteres :max',
    ];
}