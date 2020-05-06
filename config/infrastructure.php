<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Config Global Acl
    |--------------------------------------------------------------------------
    |
    | By default, database results will be returned as instances of the PHP
    | stdClass object; however, you may desire to retrieve records in an
    | array format for simplicity. Here you can tweak the fetch style.
    |
    */

    'global' => [
        'prefix' => '',
        'timeout' => 5.0,
        'doc_point' => '/doc',
        'domain' => ''
    ],

    'services' => [
        'name_service' => [
            'base_url' => 'path_base_url_service'
        ]
    ]
];