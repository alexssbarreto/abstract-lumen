<?php

namespace Abtechi\Validators;


abstract class AbstractValidator
{

    public static $rules = [];

    public static $messages = [
        'required' => 'Atributo é obrigatório.',
        'email' => 'E-mail inválido.'
    ];
}