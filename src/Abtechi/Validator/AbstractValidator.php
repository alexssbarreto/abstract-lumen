<?php

namespace Abtechi\Laravel\Validators;

/**
 * Classe base para realização das validações da Request
 * Class AbstractValidator
 * @package Abtechi\Laravel\Validators
 */
abstract class AbstractValidator
{

    public static $rules = [];

    public static $messages = [
        'required' => 'Atributo é obrigatório.',
        'email' => 'E-mail inválido.'
    ];
}