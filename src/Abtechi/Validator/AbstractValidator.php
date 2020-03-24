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
        'email' => 'E-mail inválido.'
    ];
}